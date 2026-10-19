<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Configure Midtrans global parameters.
     */
    public function configure(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('midtrans.is_3ds', true);
    }

    /**
     * Generate Snap Token for an Order.
     *
     * @throws Exception
     */
    public function createSnapToken(Order $order): string
    {
        // If order already has a real snap token, reuse it
        if (! empty($order->snap_token) && ! str_starts_with($order->snap_token, 'MOCK-SNAP-')) {
            return $order->snap_token;
        }

        $this->configure();

        $user = $order->user;
        $workshop = $order->workshop;

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_code,
                'gross_amount' => (int) $order->amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number ?: '08123456789',
            ],
            'item_details' => [
                [
                    'id' => 'WS-'.$workshop->id,
                    'price' => (int) $order->amount,
                    'quantity' => 1,
                    'name' => mb_substr($workshop->title, 0, 50),
                ],
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);

            return $snapToken;
        } catch (Exception $e) {
            // For offline / development testing without valid Midtrans key, return mock token
            if (str_contains(config('midtrans.server_key'), 'sampleKey')) {
                $mockToken = 'MOCK-SNAP-'.bin2hex(random_bytes(16));
                $order->update(['snap_token' => $mockToken]);

                return $mockToken;
            }

            throw $e;
        }
    }

    /**
     * Verify Midtrans SHA512 signature key.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Get Client Key for Frontend Snap JS.
     */
    public function getClientKey(): string
    {
        return config('midtrans.client_key');
    }

    /**
     * Get Midtrans Snap JS URL.
     */
    public function getSnapJsUrl(): string
    {
        return config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Registration;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(protected MidtransService $midtransService) {}

    /**
     * Handle incoming notification webhook from Midtrans.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received', $payload);

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? 'online';
        $transactionId = $payload['transaction_id'] ?? null;

        if (empty($orderId)) {
            return response()->json(['message' => 'Missing order_id'], 400);
        }

        // Verify signature (allow bypass if using sample demo key in local environment)
        $isLocalDemo = str_contains(config('midtrans.server_key'), 'sampleKey');
        if (! $isLocalDemo && ! $this->midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans Webhook Invalid Signature', ['order_id' => $orderId]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $order = Order::with(['workshop', 'user'])->where('order_code', $orderId)->first();

        if (! $order) {
            Log::warning('Midtrans Webhook Order Not Found', ['order_code' => $orderId]);

            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($order, $transactionStatus, $fraudStatus, $paymentType, $transactionId, $payload): void {
            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $this->markOrderAsPaid($order, $paymentType, $transactionId, $payload);
                }
            } elseif ($transactionStatus === 'settlement') {
                $this->markOrderAsPaid($order, $paymentType, $transactionId, $payload);
            } elseif ($transactionStatus === 'pending') {
                $order->update([
                    'status' => 'pending',
                    'payment_type' => $paymentType,
                    'payment_payload' => $payload,
                ]);
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'], true)) {
                $newStatus = match ($transactionStatus) {
                    'expire' => 'expired',
                    'cancel' => 'cancelled',
                    default => 'failed',
                };

                $order->update([
                    'status' => $newStatus,
                    'payment_payload' => $payload,
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Payment notification processed successfully',
        ]);
    }

    /**
     * Mark order as paid and generate registration ticket.
     *
     * @param  array<string, mixed>  $payload
     */
    protected function markOrderAsPaid(Order $order, string $paymentType, ?string $transactionId, array $payload): void
    {
        if ($order->status === 'paid') {
            return;
        }

        $order->update([
            'status' => 'paid',
            'payment_type' => $paymentType,
            'midtrans_transaction_id' => $transactionId,
            'paid_at' => now(),
            'payment_payload' => $payload,
        ]);

        // Issue Registration (Ticket)
        Registration::firstOrCreate(
            [
                'user_id' => $order->user_id,
                'workshop_id' => $order->workshop_id,
            ],
            [
                'order_id' => $order->id,
                'attendance_status' => 'not_attended',
                'notes' => 'Pembayaran lunas via Midtrans ('.strtoupper($paymentType).')',
            ]
        );

        // Decrement quota
        $workshop = $order->workshop;
        if ($workshop && $workshop->remaining_quota > 0) {
            $workshop->decrement('remaining_quota');
        }
    }
}

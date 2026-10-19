<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_midtrans_settlement_webhook_marks_order_paid_and_creates_ticket(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Dani',
            'email' => 'ahmad@example.com',
        ]);

        $workshop = Workshop::create([
            'title' => 'Kubernetes in Action',
            'slug' => 'kubernetes-in-action',
            'type' => 'bootcamp',
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(12),
            'status' => 'published',
            'is_paid' => true,
            'price' => 250000,
            'quota' => 20,
            'remaining_quota' => 20,
            'meeting_url' => 'https://meet.google.com/k8s-room',
        ]);

        $order = Order::create([
            'order_code' => 'INV-WK-202610-99999',
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'amount' => 250000,
            'status' => 'pending',
        ]);

        $serverKey = config('midtrans.server_key');
        $signatureKey = hash('sha512', $order->order_code.'200'.'250000.00'.$serverKey);

        $payload = [
            'order_id' => $order->order_code,
            'status_code' => '200',
            'gross_amount' => '250000.00',
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'mid-txn-random-12345',
        ];

        $response = $this->postJson('/api/midtrans/webhook', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals('qris', $order->fresh()->payment_type);
        $this->assertNotNull($order->fresh()->paid_at);

        // Ticket created
        $this->assertDatabaseHas('registrations', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'attendance_status' => 'not_attended',
        ]);

        // Quota decremented
        $this->assertEquals(19, $workshop->fresh()->remaining_quota);
    }
}

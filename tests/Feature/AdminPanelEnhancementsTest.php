<?php

namespace Tests\Feature;

use App\Filament\Pages\QuickCheckIn;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\RegistrationResource\Pages\ListRegistrations;
use App\Models\Category;
use App\Models\Order;
use App\Models\Registration;
use App\Models\Speaker;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
        ]);
    }

    private function createWorkshop(): Workshop
    {
        $category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-dev',
        ]);

        $speaker = Speaker::create([
            'name' => 'John Doe',
            'title_role' => 'Tech Lead',
        ]);

        return Workshop::create([
            'category_id' => $category->id,
            'speaker_id' => $speaker->id,
            'title' => 'Laravel 12 Full-Stack Workshop',
            'slug' => 'laravel-12-fullstack',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2)->addHours(3),
            'timezone' => 'WIB',
            'platform' => 'zoom',
            'is_paid' => false,
            'status' => 'published',
        ]);
    }

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/admin/login');
    }

    public function test_participant_cannot_access_user_management(): void
    {
        $participant = User::factory()->create([
            'role' => 'participant',
        ]);

        $response = $this->actingAs($participant)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_user_management(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Daftar Pengguna');
    }

    public function test_admin_can_access_quick_checkin_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/quick-check-in');

        $response->assertStatus(200);
        $response->assertSee('Presensi &amp; Check-in Tiket Hari-H', false);
        $response->assertSee('Scan Barcode');
    }

    public function test_admin_can_process_ticket_checkin(): void
    {
        $admin = $this->createAdmin();
        $workshop = $this->createWorkshop();
        $participant = User::factory()->create();

        $registration = Registration::create([
            'user_id' => $participant->id,
            'workshop_id' => $workshop->id,
            'ticket_code' => 'TKT-TEST-12345',
            'attendance_status' => 'not_attended',
        ]);

        $this->actingAs($admin);

        Livewire::test(QuickCheckIn::class)
            ->set('selectedWorkshopId', $workshop->id)
            ->set('ticketCode', 'TKT-TEST-12345')
            ->call('processCheckIn')
            ->assertSet('scanStatus', 'success')
            ->assertSee($participant->name);

        $this->assertTrue($registration->fresh()->isAttended());
        $this->assertNotNull($registration->fresh()->certificate_code);
    }

    public function test_checkin_detects_already_attended_ticket(): void
    {
        $admin = $this->createAdmin();
        $workshop = $this->createWorkshop();
        $participant = User::factory()->create();

        Registration::create([
            'user_id' => $participant->id,
            'workshop_id' => $workshop->id,
            'ticket_code' => 'TKT-ATTENDED-99',
            'attendance_status' => 'attended',
            'attended_at' => now()->subHour(),
        ]);

        $this->actingAs($admin);

        Livewire::test(QuickCheckIn::class)
            ->set('selectedWorkshopId', $workshop->id)
            ->set('ticketCode', 'TKT-ATTENDED-99')
            ->call('processCheckIn')
            ->assertSet('scanStatus', 'already_attended')
            ->assertSee('Perhatian: Tiket sudah digunakan');
    }

    public function test_checkin_rejects_invalid_ticket(): void
    {
        $admin = $this->createAdmin();
        $this->createWorkshop();

        $this->actingAs($admin);

        Livewire::test(QuickCheckIn::class)
            ->set('ticketCode', 'TKT-NONEXISTENT')
            ->call('processCheckIn')
            ->assertSet('scanStatus', 'not_found');
    }

    public function test_admin_can_export_registrations_csv(): void
    {
        $admin = $this->createAdmin();
        $workshop = $this->createWorkshop();
        $participant = User::factory()->create([
            'name' => 'Peserta Unik Export',
        ]);

        Registration::create([
            'user_id' => $participant->id,
            'workshop_id' => $workshop->id,
            'ticket_code' => 'TKT-CSV-001',
            'attendance_status' => 'not_attended',
        ]);

        $this->actingAs($admin);

        $livewire = Livewire::test(ListRegistrations::class);
        $livewire->callTableAction('export_csv');
        $livewire->assertFileDownloaded();
    }

    public function test_admin_can_export_orders_csv(): void
    {
        $admin = $this->createAdmin();
        $workshop = $this->createWorkshop();
        $participant = User::factory()->create([
            'name' => 'Peserta Order Export',
        ]);

        Order::create([
            'user_id' => $participant->id,
            'workshop_id' => $workshop->id,
            'order_code' => 'INV-TEST-001',
            'amount' => 150000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->actingAs($admin);

        $livewire = Livewire::test(ListOrders::class);
        $livewire->callTableAction('export_csv');
        $livewire->assertFileDownloaded();
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\Pages\Participant\MyTickets;
use App\Models\Category;
use App\Models\Registration;
use App\Models\Speaker;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OnlineWorkshopAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Workshop $workshop;

    private Registration $registration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'participant',
        ]);

        $category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
        ]);

        $speaker = Speaker::create([
            'name' => 'Sandhika Galih',
            'title_role' => 'Educator',
        ]);

        $this->workshop = Workshop::create([
            'title' => 'Laravel 12 Deep Dive',
            'slug' => 'laravel-12-deep-dive',
            'category_id' => $category->id,
            'speaker_id' => $speaker->id,
            'type' => 'webinar',
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay()->addHours(2),
            'status' => 'published',
            'is_paid' => false,
            'price' => 0,
            'quota' => 100,
            'remaining_quota' => 100,
            'meeting_url' => 'https://zoom.us/j/987654321',
            'attendance_mode' => 'token',
            'attendance_token' => 'ILKOM2026',
            'attendance_is_active' => true,
        ]);

        $this->registration = Registration::create([
            'user_id' => $this->user->id,
            'workshop_id' => $this->workshop->id,
            'ticket_code' => 'TKT-TEST-001',
            'attendance_status' => 'not_attended',
        ]);
    }

    public function test_participant_can_claim_attendance_with_valid_token(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MyTickets::class)
            ->set("inputTokens.{$this->registration->id}", 'ILKOM2026')
            ->call('claimAttendance', $this->registration->id)
            ->assertSee('Presensi berhasil dikonfirmasi');

        $this->registration->refresh();
        $this->assertTrue($this->registration->isAttended());
        $this->assertNotNull($this->registration->attended_at);
        $this->assertNotNull($this->registration->certificate_code);
    }

    public function test_token_validation_is_case_insensitive(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MyTickets::class)
            ->set("inputTokens.{$this->registration->id}", 'ilkom2026')
            ->call('claimAttendance', $this->registration->id)
            ->assertSee('Presensi berhasil dikonfirmasi');

        $this->registration->refresh();
        $this->assertTrue($this->registration->isAttended());
    }

    public function test_participant_cannot_claim_attendance_with_invalid_token(): void
    {
        $this->actingAs($this->user);

        Livewire::test(MyTickets::class)
            ->set("inputTokens.{$this->registration->id}", 'SALAH-TOKEN')
            ->call('claimAttendance', $this->registration->id)
            ->assertSee('Kode token presensi salah');

        $this->registration->refresh();
        $this->assertFalse($this->registration->isAttended());
        $this->assertNull($this->registration->certificate_code);
    }

    public function test_participant_cannot_claim_attendance_when_gate_is_closed(): void
    {
        $this->workshop->update(['attendance_is_active' => false]);
        $this->actingAs($this->user);

        Livewire::test(MyTickets::class)
            ->set("inputTokens.{$this->registration->id}", 'ILKOM2026')
            ->call('claimAttendance', $this->registration->id)
            ->assertSee('Gerbang presensi untuk workshop ini belum dibuka');

        $this->registration->refresh();
        $this->assertFalse($this->registration->isAttended());
    }

    public function test_auto_attendance_when_joining_room_in_join_room_mode(): void
    {
        $this->workshop->update([
            'attendance_mode' => 'join_room',
            'attendance_is_active' => true,
        ]);
        $this->actingAs($this->user);

        Livewire::test(MyTickets::class)
            ->call('joinRoomAndCheckIn', $this->registration->id)
            ->assertRedirect($this->workshop->meeting_url);

        $this->registration->refresh();
        $this->assertTrue($this->registration->isAttended());
        $this->assertNotNull($this->registration->certificate_code);
    }
}

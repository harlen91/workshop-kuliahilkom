<?php

namespace Tests\Feature;

use App\Livewire\Pages\WorkshopDetail;
use App\Models\Category;
use App\Models\Registration;
use App\Models\Speaker;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class WorkshopRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_register_for_free_workshop(): void
    {
        $category = Category::create([
            'name' => 'Data Science',
            'slug' => 'data-science',
        ]);

        $speaker = Speaker::create([
            'name' => 'Dr. Alan Turing',
            'title_role' => 'Data Scientist',
        ]);

        $workshop = Workshop::create([
            'title' => 'Intro to Python for Data Science',
            'slug' => 'intro-to-python-for-data-science',
            'category_id' => $category->id,
            'speaker_id' => $speaker->id,
            'type' => 'webinar',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2)->addHours(2),
            'status' => 'published',
            'is_paid' => false,
            'price' => 0,
            'quota' => 50,
            'remaining_quota' => 50,
            'meeting_url' => 'https://zoom.us/j/123456789',
            'meeting_password' => 'secret123',
        ]);

        Livewire::test(WorkshopDetail::class, ['slug' => $workshop->slug])
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi.santoso@example.com')
            ->set('phone_number', '08123456789')
            ->set('institution', 'Universitas Gadjah Mada')
            ->call('processRegistration')
            ->assertRedirect(route('participant.tickets'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi.santoso@example.com',
            'name' => 'Budi Santoso',
        ]);

        $this->assertDatabaseHas('registrations', [
            'workshop_id' => $workshop->id,
            'attendance_status' => 'not_attended',
        ]);

        $createdUser = User::where('email', 'budi.santoso@example.com')->first();
        $this->assertTrue(Hash::check('08123456789', $createdUser->password));

        $this->assertEquals(49, $workshop->fresh()->remaining_quota);
    }

    public function test_authenticated_participant_can_view_their_ticket(): void
    {
        $user = User::factory()->create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
        ]);

        $workshop = Workshop::create([
            'title' => 'Fullstack Laravel Bootcamp',
            'slug' => 'fullstack-laravel-bootcamp',
            'type' => 'workshop',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(3),
            'status' => 'published',
            'is_paid' => true,
            'price' => 150000,
            'meeting_url' => 'https://zoom.us/j/999888777',
            'meeting_password' => 'zoomPass123',
        ]);

        Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'ticket_code' => 'TKT-TEST-001',
            'attendance_status' => 'not_attended',
        ]);

        $response = $this->actingAs($user)->get(route('participant.tickets'));

        $response->assertStatus(200);
        $response->assertSee('TKT-TEST-001');
        $response->assertSee('Fullstack Laravel Bootcamp');
        $response->assertSee('zoomPass123');
    }
}

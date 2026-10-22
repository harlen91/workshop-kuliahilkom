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
        $this->assertEquals('budi.santoso@example.com', session('account_created_notice')['email'] ?? null);
        $this->assertEquals('08123456789', session('account_created_notice')['phone'] ?? null);
    }

    public function test_authenticated_participant_can_view_their_ticket(): void
    {
        $user = User::factory()->create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'phone_number' => '081298765432',
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

        $response = $this->actingAs($user)
            ->withSession([
                'account_created_notice' => [
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                ],
            ])
            ->get(route('participant.tickets'));

        $response->assertStatus(200);
        $response->assertSee('TKT-TEST-001');
        $response->assertSee('Fullstack Laravel Bootcamp');
        $response->assertSee('zoomPass123');
        $response->assertSee('Informasi Akun Login Portal Peserta');
        $response->assertSee($user->email);
        $response->assertSee($user->phone_number);
    }

    public function test_login_page_displays_default_password_hint(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Nomor WhatsApp');
        $response->assertSee('Password default');
    }

    public function test_meeting_url_attribute_normalizes_links_without_http_protocol(): void
    {
        $workshop = new Workshop([
            'meeting_url' => 'meet.google.com/xyz-1234-abc',
        ]);

        $this->assertEquals('https://meet.google.com/xyz-1234-abc', $workshop->meeting_url);

        $emptyWorkshop = new Workshop([
            'meeting_url' => '#',
        ]);
        $this->assertNull($emptyWorkshop->meeting_url);
    }

    public function test_ticket_view_shows_room_link_when_provided_and_notice_when_empty(): void
    {
        $user = User::factory()->create();

        $workshopWithLink = Workshop::create([
            'title' => 'Gmeet Workshop',
            'slug' => 'gmeet-workshop',
            'platform' => 'gmeet',
            'meeting_url' => 'meet.google.com/abc-def-ghi',
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(1)->addHours(2),
            'status' => 'published',
            'is_paid' => false,
            'price' => 0,
        ]);

        Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshopWithLink->id,
            'ticket_code' => 'TKT-GMEET-01',
            'attendance_status' => 'not_attended',
        ]);

        $response = $this->actingAs($user)->get(route('participant.tickets'));
        $response->assertStatus(200);
        $response->assertSee('href="https://meet.google.com/abc-def-ghi"', false);
        $response->assertSee('Masuk ke Room Gmeet');

        // Test with empty meeting link
        $workshopNoLink = Workshop::create([
            'title' => 'Pending Meet Workshop',
            'slug' => 'pending-meet-workshop',
            'platform' => 'gmeet',
            'meeting_url' => null,
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2)->addHours(2),
            'status' => 'published',
            'is_paid' => false,
            'price' => 0,
        ]);

        Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshopNoLink->id,
            'ticket_code' => 'TKT-GMEET-02',
            'attendance_status' => 'not_attended',
        ]);

        $response2 = $this->actingAs($user)->get(route('participant.tickets'));
        $response2->assertStatus(200);
        $response2->assertSee('Link room Gmeet belum disematkan');
        // Ensure fake href="#" is NOT rendered
        $response2->assertDontSee('href="#"', false);
    }
}

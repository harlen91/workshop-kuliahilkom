<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Registration;
use App\Models\Speaker;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopWhatsappGroupTest extends TestCase
{
    use RefreshDatabase;

    private function createWorkshop(array $attributes = []): Workshop
    {
        $category = Category::create([
            'name' => 'Web Development',
            'slug' => 'web-dev-'.uniqid(),
        ]);

        $speaker = Speaker::create([
            'name' => 'John Doe',
            'title_role' => 'Software Architect',
        ]);

        return Workshop::create(array_merge([
            'category_id' => $category->id,
            'speaker_id' => $speaker->id,
            'title' => 'Mastering Laravel 12 & Livewire 3',
            'slug' => 'mastering-laravel-12-'.uniqid(),
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(3)->addHours(2),
            'timezone' => 'WIB',
            'platform' => 'zoom',
            'meeting_url' => 'https://zoom.us/j/123456789',
            'whatsapp_group_url' => 'https://chat.whatsapp.com/TestGroup123',
            'is_paid' => false,
            'price' => 0,
            'status' => 'published',
        ], $attributes));
    }

    public function test_workshop_model_normalizes_whatsapp_group_url(): void
    {
        // 1. Without scheme: auto-prefix https://
        $ws1 = $this->createWorkshop([
            'whatsapp_group_url' => 'chat.whatsapp.com/InviteAbcXYZ',
        ]);
        $this->assertEquals('https://chat.whatsapp.com/InviteAbcXYZ', $ws1->whatsapp_group_url);
        $this->assertTrue($ws1->hasWhatsappGroup());

        // 2. Empty or # -> null
        $ws2 = $this->createWorkshop([
            'whatsapp_group_url' => '#',
        ]);
        $this->assertNull($ws2->whatsapp_group_url);
        $this->assertFalse($ws2->hasWhatsappGroup());

        $ws3 = $this->createWorkshop([
            'whatsapp_group_url' => '',
        ]);
        $this->assertNull($ws3->whatsapp_group_url);
        $this->assertFalse($ws3->hasWhatsappGroup());
    }

    public function test_workshop_invitation_text_helper(): void
    {
        $ws = $this->createWorkshop([
            'whatsapp_group_url' => 'https://chat.whatsapp.com/GroupJoinUrl',
        ]);

        $text = $ws->getWhatsappGroupInvitationText('Budi');
        $this->assertStringContainsString('Halo Budi,', $text);
        $this->assertStringContainsString('https://chat.whatsapp.com/GroupJoinUrl', $text);
    }

    public function test_participant_tickets_page_shows_whatsapp_group_button(): void
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $workshop = $this->createWorkshop([
            'whatsapp_group_url' => 'https://chat.whatsapp.com/KeluargaKuliahIlkom',
        ]);

        Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'attendance_status' => 'not_attended',
        ]);

        $response = $this->actingAs($user)->get('/participant/my-tickets');

        $response->assertStatus(200);
        $response->assertSee('Grup WhatsApp Peserta');
        $response->assertSee('Gabung Grup WA');
        $response->assertSee('https://chat.whatsapp.com/KeluargaKuliahIlkom');
    }

    public function test_participant_tickets_page_handles_missing_whatsapp_group_gracefully(): void
    {
        $user = User::factory()->create([
            'role' => 'participant',
        ]);

        $workshop = $this->createWorkshop([
            'whatsapp_group_url' => null,
        ]);

        Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'attendance_status' => 'not_attended',
        ]);

        $response = $this->actingAs($user)->get('/participant/my-tickets');

        $response->assertStatus(200);
        $response->assertSee('Tautan grup WhatsApp akan segera dibagikan oleh panitia sebelum acara dimulai.');
    }

    public function test_workshop_detail_shows_whatsapp_group_in_facility_list(): void
    {
        $workshop = $this->createWorkshop();

        $response = $this->get('/workshop/'.$workshop->slug);

        $response->assertStatus(200);
        $response->assertSee('Grup Komunikasi dan Diskusi WhatsApp Peserta');
    }

    public function test_broadcast_modal_view_includes_whatsapp_group_link(): void
    {
        $workshop = $this->createWorkshop([
            'whatsapp_group_url' => 'https://chat.whatsapp.com/BroadcastGroupTest',
        ]);

        $view = $this->view('filament.modals.workshop-broadcast-info', [
            'record' => $workshop,
            'contacts' => 'Peserta Satu - 081234567890',
            'phoneList' => '6281234567890',
        ]);

        $view->assertSee('https://chat.whatsapp.com/BroadcastGroupTest');
        $view->assertSee('Template 1: Broadcast Undangan WhatsApp Group');
        $view->assertSee('Template 2: Broadcast Reminder Lengkap Event');
    }

    public function test_whatsapp_chat_action_url_generation_includes_group_invite(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Dani',
            'phone_number' => '081234567890',
        ]);

        $workshop = $this->createWorkshop([
            'whatsapp_group_url' => 'https://chat.whatsapp.com/GrupKeren2026',
        ]);

        $reg = Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'attendance_status' => 'not_attended',
        ]);

        $phone = '6281234567890';
        $groupInvite = "\n\nSilakan bergabung ke Grup WhatsApp Peserta:\n{$workshop->whatsapp_group_url}";
        $text = "Halo {$reg->user->name}, tiket event '{$workshop->title}' Anda telah terdaftar. Kode Tiket: {$reg->ticket_code}.{$groupInvite}\n\nSampai jumpa di acara!";
        $expectedUrl = "https://wa.me/{$phone}?text=".urlencode($text);

        $this->assertStringContainsString('https%3A%2F%2Fchat.whatsapp.com%2FGrupKeren2026', $expectedUrl);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Registration;
use App\Models\Speaker;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificatePdfTest extends TestCase
{
    use RefreshDatabase;

    private function createWorkshop(array $attributes = []): Workshop
    {
        $category = Category::create([
            'name' => 'Backend Engineering',
            'slug' => 'backend-'.uniqid(),
        ]);

        $speaker = Speaker::create([
            'name' => 'Budi Santoso',
            'title_role' => 'Principal Engineer',
        ]);

        return Workshop::create(array_merge([
            'category_id' => $category->id,
            'speaker_id' => $speaker->id,
            'title' => 'Arsitektur Laravel 12 Skala Produksi',
            'slug' => 'arsitektur-laravel-12-'.uniqid(),
            'start_date' => now()->addDay(),
            'end_date' => now()->addDay()->addHours(3),
            'timezone' => 'WIB',
            'platform' => 'zoom',
            'is_paid' => false,
            'price' => 0,
            'status' => 'published',
            'certificate_enabled' => true,
        ], $attributes));
    }

    private function createRegistration(User $user, Workshop $workshop, array $attributes = []): Registration
    {
        return Registration::create(array_merge([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'attendance_status' => 'attended',
            'attended_at' => now(),
            'certificate_code' => 'CERT-20261125-ABC123',
        ], $attributes));
    }

    public function test_attended_participant_can_download_certificate_pdf(): void
    {
        $participant = User::factory()->create([
            'name' => 'Siti Rahmawati',
            'institution' => 'Universitas Indonesia',
            'role' => 'participant',
        ]);
        $workshop = $this->createWorkshop();
        $registration = $this->createRegistration($participant, $workshop);

        $response = $this->actingAs($participant)->get(route('certificates.download', $registration));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $contentDisposition = (string) $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', $contentDisposition);
        $this->assertStringContainsString('.pdf', $contentDisposition);
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_attended_participant_can_preview_certificate_pdf_inline(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $workshop = $this->createWorkshop();
        $registration = $this->createRegistration($participant, $workshop);

        $response = $this->actingAs($participant)->get(route('certificates.preview', $registration));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_guest_is_redirected_to_login_when_downloading_or_previewing_certificate(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $workshop = $this->createWorkshop();
        $registration = $this->createRegistration($participant, $workshop);

        $this->get(route('certificates.download', $registration))
            ->assertRedirect(route('login'));

        $this->get(route('certificates.preview', $registration))
            ->assertRedirect(route('login'));
    }

    public function test_other_participant_cannot_download_or_preview_another_users_certificate(): void
    {
        $owner = User::factory()->create(['role' => 'participant']);
        $otherUser = User::factory()->create(['role' => 'participant']);
        $workshop = $this->createWorkshop();
        $registration = $this->createRegistration($owner, $workshop);

        $this->actingAs($otherUser)
            ->get(route('certificates.download', $registration))
            ->assertStatus(403);

        $this->actingAs($otherUser)
            ->get(route('certificates.preview', $registration))
            ->assertStatus(403);
    }

    public function test_admin_can_download_any_participants_certificate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = User::factory()->create(['role' => 'participant']);
        $workshop = $this->createWorkshop();
        $registration = $this->createRegistration($participant, $workshop);

        $response = $this->actingAs($admin)->get(route('certificates.download', $registration));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_registration_without_certificate_code_returns_404(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $workshop = $this->createWorkshop();
        $registration = $this->createRegistration($participant, $workshop, [
            'attendance_status' => 'not_attended',
            'attended_at' => null,
            'certificate_code' => null,
        ]);

        $this->actingAs($participant)
            ->get(route('certificates.download', $registration))
            ->assertStatus(404);
    }

    public function test_disabled_certificate_workshop_returns_404_and_mark_attended_does_not_issue_code(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $workshop = $this->createWorkshop(['certificate_enabled' => false]);

        $registration = Registration::create([
            'user_id' => $participant->id,
            'workshop_id' => $workshop->id,
            'attendance_status' => 'not_attended',
        ]);

        $registration->markAttended();
        $registration->refresh();

        $this->assertTrue($registration->isAttended());
        $this->assertNull($registration->certificate_code);
        $this->assertFalse($registration->hasCertificate());

        // Even if a certificate_code is manually populated, download stays blocked when disabled
        $registration->update(['certificate_code' => 'CERT-DISABLED-001']);

        $this->actingAs($participant)
            ->get(route('certificates.download', $registration))
            ->assertStatus(404);
    }

    public function test_public_verification_page_renders_search_form(): void
    {
        $response = $this->get(route('certificates.verify'));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Keaslian E-Sertifikat');
        $response->assertSee('Verifikasi Sertifikat');
    }

    public function test_public_verification_displays_valid_certificate_without_exposing_sensitive_contact_info(): void
    {
        $participant = User::factory()->create([
            'name' => 'Andi Pratama',
            'email' => 'andi.rahasia@example.com',
            'phone_number' => '081299998888',
            'institution' => 'Institut Teknologi Bandung',
        ]);
        $workshop = $this->createWorkshop(['title' => 'Cloud Native DevOps']);

        foreach (['CERT-20261125-XYZ789', 'CERT/ILKOM/202611/00001'] as $code) {
            $reg = $this->createRegistration($participant, $this->createWorkshop(['title' => 'Cloud Native DevOps']), [
                'certificate_code' => $code,
            ]);

            $response = $this->get(route('certificates.verify', ['code' => $reg->certificate_code]));

            $response->assertStatus(200);
            $response->assertSee('Sertifikat Terverifikasi');
            $response->assertSee('Andi Pratama');
            $response->assertSee('Institut Teknologi Bandung');
            $response->assertSee('Cloud Native DevOps');
            $response->assertSee($code);
            $response->assertDontSee('andi.rahasia@example.com');
            $response->assertDontSee('081299998888');
        }
    }

    public function test_public_verification_shows_not_found_for_invalid_or_disabled_certificate(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);
        $disabledWorkshop = $this->createWorkshop(['certificate_enabled' => false]);
        $this->createRegistration($participant, $disabledWorkshop, [
            'certificate_code' => 'CERT-DISABLED-999',
        ]);

        $this->get(route('certificates.verify', ['code' => 'CERT-TIDAK-ADA']))
            ->assertStatus(200)
            ->assertSee('Sertifikat Tidak Ditemukan');

        $this->get(route('certificates.verify', ['code' => 'CERT-DISABLED-999']))
            ->assertStatus(200)
            ->assertSee('Sertifikat Tidak Ditemukan');
    }

    public function test_my_tickets_shows_download_button_only_when_certificate_is_available(): void
    {
        $participant = User::factory()->create(['role' => 'participant']);

        $eligibleWorkshop = $this->createWorkshop(['certificate_enabled' => true]);
        $eligibleReg = $this->createRegistration($participant, $eligibleWorkshop, [
            'certificate_code' => 'CERT-20261125-ELIGIB',
        ]);

        $pendingWorkshop = $this->createWorkshop(['certificate_enabled' => true]);
        $pendingReg = $this->createRegistration($participant, $pendingWorkshop, [
            'attendance_status' => 'not_attended',
            'attended_at' => null,
            'certificate_code' => null,
        ]);

        $disabledWorkshop = $this->createWorkshop(['certificate_enabled' => false]);
        $disabledReg = $this->createRegistration($participant, $disabledWorkshop, [
            'certificate_code' => 'CERT-20261125-DISABL',
        ]);

        $response = $this->actingAs($participant)->get(route('participant.tickets'));

        $response->assertStatus(200);
        $response->assertSee(route('certificates.download', $eligibleReg));
        $response->assertSee('Unduh Sertifikat (PDF)');
        $response->assertDontSee(route('certificates.download', $pendingReg));
        $response->assertDontSee(route('certificates.download', $disabledReg));
    }

    public function test_pdf_generation_supports_custom_background_and_signature_images(): void
    {
        Storage::fake('public');

        $tinyPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        Storage::disk('public')->put('workshops/certificates/signatures/sign.png', $tinyPng);
        Storage::disk('public')->put('workshops/certificates/backgrounds/bg.png', $tinyPng);

        $participant = User::factory()->create(['role' => 'participant']);
        $workshop = $this->createWorkshop([
            'certificate_signer_name' => 'Dr. Hendra Wijaya',
            'certificate_signer_title' => 'Ketua Program KuliahIlkom',
            'certificate_signature_path' => 'workshops/certificates/signatures/sign.png',
            'certificate_background_path' => 'workshops/certificates/backgrounds/bg.png',
        ]);
        $registration = $this->createRegistration($participant, $workshop);

        $response = $this->actingAs($participant)->get(route('certificates.download', $registration));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_registration_policy_download_certificate_permissions(): void
    {
        $owner = User::factory()->create(['role' => 'participant']);
        $other = User::factory()->create(['role' => 'participant']);
        $admin = User::factory()->create(['role' => 'admin']);
        $workshop = $this->createWorkshop();
        $registration = $this->createRegistration($owner, $workshop);

        $this->assertTrue($owner->can('downloadCertificate', $registration));
        $this->assertTrue($admin->can('downloadCertificate', $registration));
        $this->assertFalse($other->can('downloadCertificate', $registration));
    }
}

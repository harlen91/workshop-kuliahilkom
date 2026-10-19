<?php

namespace Tests\Feature;

use App\Filament\Resources\SpeakerResource\Pages\CreateSpeaker;
use App\Filament\Resources\WorkshopResource\Pages\CreateWorkshop;
use App\Models\Category;
use App\Models\Speaker;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class WorkshopImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_workshop_banner_accessor_resolves_storage_url(): void
    {
        $workshop = Workshop::create([
            'title' => 'Sample Workshop',
            'slug' => 'sample-workshop',
            'type' => 'workshop',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2)->addHours(2),
            'status' => 'published',
            'banner_path' => 'workshops/banners/my-banner.jpg',
        ]);

        $this->assertStringContainsString('storage/workshops/banners/my-banner.jpg', $workshop->banner_url);
    }

    public function test_speaker_photo_accessor_resolves_storage_url(): void
    {
        $speaker = Speaker::create([
            'name' => 'Budi Mentor',
            'title_role' => 'Senior Developer',
            'photo_url' => 'speakers/photos/budi.jpg',
        ]);

        $this->assertStringContainsString('storage/speakers/photos/budi.jpg', $speaker->photo_url);
    }

    public function test_admin_can_upload_speaker_photo_via_filament(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);

        $file = UploadedFile::fake()->image('instructor.jpg', 400, 400);

        Livewire::actingAs($admin)
            ->test(CreateSpeaker::class)
            ->fillForm([
                'name' => 'Prof. Eko',
                'title_role' => 'System Architect',
                'photo_url' => $file,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $speaker = Speaker::where('name', 'Prof. Eko')->first();
        $this->assertNotNull($speaker);
        $this->assertNotNull($speaker->getRawOriginal('photo_url'));

        Storage::disk('public')->assertExists($speaker->getRawOriginal('photo_url'));
    }

    public function test_admin_can_upload_workshop_banner_via_filament(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin2@test.com',
        ]);

        $category = Category::create([
            'name' => 'Cloud',
            'slug' => 'cloud',
        ]);

        $speaker = Speaker::create([
            'name' => 'Pak Rudi',
            'title_role' => 'Cloud Specialist',
        ]);

        $file = UploadedFile::fake()->image('banner.jpg', 1280, 720);

        Livewire::actingAs($admin)
            ->test(CreateWorkshop::class)
            ->fillForm([
                'title' => 'AWS Serverless Workshop',
                'slug' => 'aws-serverless-workshop',
                'category_id' => $category->id,
                'speaker_id' => $speaker->id,
                'type' => 'workshop',
                'start_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'end_date' => now()->addDays(5)->addHours(3)->format('Y-m-d H:i:s'),
                'timezone' => 'WIB',
                'platform' => 'zoom',
                'is_paid' => false,
                'price' => 0,
                'status' => 'published',
                'banner_path' => $file,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $workshop = Workshop::where('slug', 'aws-serverless-workshop')->first();
        $this->assertNotNull($workshop);
        $this->assertNotNull($workshop->banner_path);

        Storage::disk('public')->assertExists($workshop->banner_path);
    }
}

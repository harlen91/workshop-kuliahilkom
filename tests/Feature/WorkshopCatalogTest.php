<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Speaker;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_published_workshops(): void
    {
        $category = Category::create([
            'name' => 'Backend Development',
            'slug' => 'backend-development',
        ]);

        $speaker = Speaker::create([
            'name' => 'John Doe',
            'title_role' => 'Principal Engineer',
        ]);

        $workshop = Workshop::create([
            'title' => 'Building Scalable APIs with Laravel',
            'slug' => 'building-scalable-apis-with-laravel',
            'category_id' => $category->id,
            'speaker_id' => $speaker->id,
            'type' => 'workshop',
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(3)->addHours(3),
            'status' => 'published',
            'is_paid' => false,
            'price' => 0,
            'quota' => 100,
            'remaining_quota' => 100,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Building Scalable APIs with Laravel');
        $response->assertSee('Backend Development');
    }

    public function test_workshop_detail_page_loads_successfully(): void
    {
        $category = Category::create([
            'name' => 'AI Engineering',
            'slug' => 'ai-engineering',
        ]);

        $speaker = Speaker::create([
            'name' => 'Jane Smith',
            'title_role' => 'AI Specialist',
        ]);

        $workshop = Workshop::create([
            'title' => 'Mastering AI Autonomous Agents',
            'slug' => 'mastering-ai-autonomous-agents',
            'category_id' => $category->id,
            'speaker_id' => $speaker->id,
            'type' => 'webinar',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(7)->addHours(2),
            'status' => 'published',
            'is_paid' => true,
            'price' => 99000,
            'quota' => 50,
            'remaining_quota' => 50,
        ]);

        $response = $this->get('/workshop/'.$workshop->slug);

        $response->assertStatus(200);
        $response->assertSee('Mastering AI Autonomous Agents');
        $response->assertSee('Jane Smith');
        $response->assertSee('Rp 99.000');
    }

    public function test_workshop_detail_renders_html_description_without_escaping_tags(): void
    {
        $workshop = Workshop::create([
            'title' => 'Refactoring Skripsi Test',
            'slug' => 'refactoring-skripsi-test',
            'type' => 'workshop',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2)->addHours(2),
            'status' => 'published',
            'is_paid' => false,
            'price' => 0,
            'description' => '<p>🎓 <strong>WORKSHOP ONLINE: REFACTORING SKRIPSI</strong></p><p>Optimalkan riset.</p>',
        ]);

        $response = $this->get('/workshop/'.$workshop->slug);

        $response->assertStatus(200);
        // Ensure literal raw HTML tags are NOT printed as escaped text entities
        $response->assertDontSee('&lt;p&gt;', false);
        $response->assertDontSee('&lt;strong&gt;', false);
        $response->assertSee('<p>🎓 <strong>WORKSHOP ONLINE: REFACTORING SKRIPSI</strong></p>', false);
    }
}

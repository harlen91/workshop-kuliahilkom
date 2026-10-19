<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Speaker;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndSitemapTest extends TestCase
{
    use RefreshDatabase;

    private function createPublishedWorkshop(): Workshop
    {
        $category = Category::create([
            'name' => 'Backend Engineering',
            'slug' => 'backend-engineering',
        ]);

        $speaker = Speaker::create([
            'name' => 'Budi Santoso',
            'title_role' => 'Principal Software Architect',
        ]);

        return Workshop::create([
            'category_id' => $category->id,
            'speaker_id' => $speaker->id,
            'title' => 'Mastering Laravel 12 Microservices',
            'slug' => 'mastering-laravel-12-microservices',
            'short_description' => 'Pelajari arsitektur microservices tingkat lanjut dengan Laravel 12 dan Redis.',
            'description' => 'Silabus lengkap mengenai arsitektur decoupled services...',
            'type' => 'workshop',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(5)->addHours(4),
            'timezone' => 'WIB',
            'platform' => 'zoom',
            'is_paid' => true,
            'price' => 199000,
            'quota' => 50,
            'remaining_quota' => 50,
            'status' => 'published',
        ]);
    }

    public function test_homepage_has_seo_meta_tags_and_canonical(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<title>Katalog Workshop &amp; Webinar IT Terdepan | KuliahIlkom</title>', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<link rel="canonical" href="'.route('home').'"', false);
        $response->assertSee('<meta property="og:site_name" content="KuliahIlkom Workshop">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('schema.org', false);
        $response->assertSee('"@type": "WebSite"', false);
        $response->assertSee('"@type": "Organization"', false);
        $response->assertSee('"@type": "FAQPage"', false);
    }

    public function test_workshop_detail_has_dynamic_seo_meta_tags(): void
    {
        $workshop = $this->createPublishedWorkshop();

        $response = $this->get('/workshop/'.$workshop->slug);

        $response->assertStatus(200);
        $response->assertSee($workshop->title.' | Workshop &amp; Webinar KuliahIlkom', false);
        $response->assertSee($workshop->short_description);
        $response->assertSee('<link rel="canonical" href="'.route('workshop.detail', $workshop->slug).'"', false);
        $response->assertSee('<meta property="og:type" content="article">', false);
        $response->assertSee('<meta property="og:title" content="'.$workshop->title.' | Workshop &amp; Webinar KuliahIlkom">', false);

        // JSON-LD Schema
        $response->assertSee('"@type": "EducationEvent"', false);
        $response->assertSee($workshop->title);
        $response->assertSee($workshop->speaker->name);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }

    public function test_sitemap_xml_returns_valid_xml_with_published_workshops(): void
    {
        $published = $this->createPublishedWorkshop();

        $draftCategory = Category::create(['name' => 'AI', 'slug' => 'ai']);
        $draftSpeaker = Speaker::create(['name' => 'Jane', 'title_role' => 'Researcher']);
        $draft = Workshop::create([
            'category_id' => $draftCategory->id,
            'speaker_id' => $draftSpeaker->id,
            'title' => 'Draft Secret AI Event',
            'slug' => 'draft-secret-ai-event',
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(10)->addHours(2),
            'status' => 'draft',
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', (string) $response->headers->get('Content-Type'));

        $content = $response->getContent();
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
        $this->assertStringContainsString(route('workshop.detail', $published->slug), $content);
        $this->assertStringNotContainsString(route('workshop.detail', $draft->slug), $content);
    }

    public function test_robots_txt_contains_sitemap_and_disallowed_paths(): void
    {
        $robotsContent = file_get_contents(public_path('robots.txt'));

        $this->assertNotFalse($robotsContent);
        $this->assertStringContainsString('Sitemap: https://workshop.kuliahilkom.com/sitemap.xml', $robotsContent);
        $this->assertStringContainsString('Disallow: /admin/', $robotsContent);
        $this->assertStringContainsString('Disallow: /participant/', $robotsContent);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap for search engines.
     */
    public function index(): Response
    {
        $workshops = Workshop::query()
            ->where('status', 'published')
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('sitemap', [
            'workshops' => $workshops,
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}

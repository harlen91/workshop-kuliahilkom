<?php

namespace App\Livewire\Pages;

use App\Models\Category;
use App\Models\Registration;
use App\Models\Workshop;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Home extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public ?string $selectedCategory = null;

    #[Url(as: 'type')]
    public ?string $selectedType = null;

    #[Url(as: 'price')]
    public ?string $selectedPrice = null;

    public function setCategory(?string $slug): void
    {
        $this->selectedCategory = $slug;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = null;
        $this->selectedType = null;
        $this->selectedPrice = null;
    }

    public function render(): View
    {
        $categories = Category::withCount(['workshops' => function ($q): void {
            $q->where('status', 'published');
        }])->get();

        $workshopsQuery = Workshop::with(['category', 'speaker'])
            ->where('status', 'published')
            ->when($this->search, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('short_description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->selectedCategory, function ($query): void {
                $query->whereHas('category', function ($q): void {
                    $q->where('slug', $this->selectedCategory);
                });
            })
            ->when($this->selectedType, function ($query): void {
                $query->where('type', $this->selectedType);
            })
            ->when($this->selectedPrice === 'free', function ($query): void {
                $query->where(function ($q): void {
                    $q->where('is_paid', false)->orWhere('price', '<=', 0);
                });
            })
            ->when($this->selectedPrice === 'paid', function ($query): void {
                $query->where('is_paid', true)->where('price', '>', 0);
            })
            ->orderBy('start_date', 'asc');

        $workshops = $workshopsQuery->get();

        $stats = [
            'total_alumni' => Registration::count() + 1540,
            'total_events' => Workshop::where('status', 'published')->count(),
            'satisfaction_rate' => '99.4%',
        ];

        return view('livewire.pages.home', [
            'categories' => $categories,
            'workshops' => $workshops,
            'stats' => $stats,
        ])->layout('layouts.app', [
            'metaTitle' => 'Katalog Workshop & Webinar IT Terdepan | KuliahIlkom',
            'metaDescription' => 'Tingkatkan skill coding, Laravel, AI, database, dan cloud computing bersama praktisi industri. Dapatkan e-sertifikat ber-QR code dan materi rekaman lengkap.',
            'metaKeywords' => 'workshop IT, webinar programming, kuliahilkom, pelatihan coding online, belajar laravel 12, kursus fullstack indonesia',
            'canonicalUrl' => route('home'),
            'ogType' => 'website',
        ]);
    }
}

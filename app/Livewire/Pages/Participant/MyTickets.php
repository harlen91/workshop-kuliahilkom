<?php

namespace App\Livewire\Pages\Participant;

use App\Models\Registration;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MyTickets extends Component
{
    public string $activeTab = 'upcoming';

    public function mount(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render(): View
    {
        $user = Auth::user();

        $registrationsQuery = Registration::with(['workshop.speaker', 'workshop.category', 'order'])
            ->where('user_id', $user->id);

        if ($this->activeTab === 'upcoming') {
            $registrations = (clone $registrationsQuery)
                ->whereHas('workshop', function ($q): void {
                    $q->where('end_date', '>=', now()->subHours(6));
                })
                ->latest()
                ->get();
        } else {
            $registrations = (clone $registrationsQuery)
                ->whereHas('workshop', function ($q): void {
                    $q->where('end_date', '<', now()->subHours(6));
                })
                ->latest()
                ->get();
        }

        return view('livewire.pages.participant.my-tickets', [
            'registrations' => $registrations,
            'user' => $user,
        ]);
    }
}

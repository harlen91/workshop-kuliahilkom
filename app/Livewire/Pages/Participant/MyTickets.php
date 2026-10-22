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

    /**
     * @var array<int, string>
     */
    public array $inputTokens = [];

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

    /**
     * Participant claims attendance using session token provided by speaker.
     */
    public function claimAttendance(int $registrationId): void
    {
        $registration = Registration::with('workshop')
            ->where('user_id', Auth::id())
            ->findOrFail($registrationId);

        if ($registration->isAttended()) {
            session()->flash('success', 'Anda sudah tercatat hadir pada workshop ini.');

            return;
        }

        $workshop = $registration->workshop;

        if (! $workshop->isAttendanceOpen()) {
            session()->flash('error', 'Gerbang presensi untuk workshop ini belum dibuka atau telah ditutup oleh panitia.');

            return;
        }

        $token = trim((string) ($this->inputTokens[$registrationId] ?? ''));

        if ($token === '') {
            session()->flash('error', 'Silakan masukkan kode token presensi terlebih dahulu.');

            return;
        }

        if (! $workshop->isAttendanceTokenValid($token)) {
            session()->flash('error', 'Kode token presensi salah atau tidak sesuai. Silakan periksa kembali!');

            return;
        }

        $registration->markAttended();
        $this->inputTokens[$registrationId] = '';

        session()->flash('success', 'Presensi berhasil dikonfirmasi! E-Sertifikat Anda telah diterbitkan.');
    }

    /**
     * Auto attendance when participant clicks "Masuk ke Room" if workshop mode is join_room.
     */
    public function joinRoomAndCheckIn(int $registrationId)
    {
        $registration = Registration::with('workshop')
            ->where('user_id', Auth::id())
            ->findOrFail($registrationId);

        $workshop = $registration->workshop;

        if ($workshop->isJoinRoomAttendance() && ! $registration->isAttended() && $workshop->isAttendanceOpen()) {
            $registration->markAttended();
            session()->flash('success', 'Kehadiran Anda berhasil dicatat otomatis saat memasuki ruang virtual!');
        }

        if ($workshop->meeting_url) {
            return redirect()->away($workshop->meeting_url);
        }
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

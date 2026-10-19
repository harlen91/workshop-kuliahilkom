<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use App\Services\MidtransService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class WorkshopDetail extends Component
{
    public Workshop $workshop;

    public string $name = '';

    public string $email = '';

    public string $phone_number = '';

    public string $institution = '';

    public bool $showRegisterModal = false;

    public bool $isRegistered = false;

    public ?Registration $registeredTicket = null;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    public function mount(string $slug): void
    {
        $this->workshop = Workshop::with(['category', 'speaker'])
            ->where('slug', $slug)
            ->firstOrFail();

        if (Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone_number = $user->phone_number ?? '';
            $this->institution = $user->institution ?? '';

            $this->checkRegistrationStatus($user->id);
        }
    }

    protected function checkRegistrationStatus(int $userId): void
    {
        $registration = Registration::where('user_id', $userId)
            ->where('workshop_id', $this->workshop->id)
            ->first();

        if ($registration) {
            $this->isRegistered = true;
            $this->registeredTicket = $registration;
        }
    }

    public function openRegistrationModal(): void
    {
        if ($this->isRegistered) {
            $this->redirectRoute('participant.tickets');

            return;
        }

        $this->errorMessage = null;
        $this->showRegisterModal = true;
    }

    public function processRegistration(MidtransService $midtransService): void
    {
        $this->errorMessage = null;

        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|min:8|max:20',
            'institution' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'phone_number.required' => 'Nomor WhatsApp wajib diisi untuk konfirmasi tiket.',
        ]);

        // Check quota availability
        if ($this->workshop->quota !== null && $this->workshop->remaining_quota <= 0) {
            $this->errorMessage = 'Mohon maaf, kuota peserta untuk workshop ini telah habis.';

            return;
        }

        // Authenticate or create user account
        $user = Auth::user();
        if (! $user) {
            $user = User::where('email', $this->email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_number' => $this->phone_number,
                    'institution' => $this->institution,
                    'password' => Hash::make($this->phone_number),
                    'role' => 'participant',
                ]);
            } else {
                // Update phone / institution if missing
                $user->update([
                    'name' => $this->name,
                    'phone_number' => $user->phone_number ?: $this->phone_number,
                    'institution' => $user->institution ?: $this->institution,
                ]);
            }

            Auth::login($user, remember: true);
        }

        // Check if user already registered
        $this->checkRegistrationStatus($user->id);
        if ($this->isRegistered) {
            $this->showRegisterModal = false;
            session()->flash('success', 'Anda sudah terdaftar untuk event ini.');
            $this->redirectRoute('participant.tickets');

            return;
        }

        // Flow A: Workshop Gratis
        if ($this->workshop->isFree()) {
            DB::transaction(function () use ($user): void {
                Registration::create([
                    'order_id' => null,
                    'user_id' => $user->id,
                    'workshop_id' => $this->workshop->id,
                    'attendance_status' => 'not_attended',
                    'notes' => 'Pendaftaran workshop gratis',
                ]);

                if ($this->workshop->remaining_quota > 0) {
                    $this->workshop->decrement('remaining_quota');
                }
            });

            $this->showRegisterModal = false;
            session()->flash('success', 'Selamat! Pendaftaran gratis Anda berhasil dikonfirmasi. Tiket dan link Zoom telah tersedia di bawah.');
            $this->redirectRoute('participant.tickets');

            return;
        }

        // Flow B: Workshop Berbayar (Midtrans Snap)
        try {
            // Find existing pending order or create new one
            $order = Order::where('user_id', $user->id)
                ->where('workshop_id', $this->workshop->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (! $order) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'workshop_id' => $this->workshop->id,
                    'amount' => $this->workshop->price,
                    'status' => 'pending',
                ]);
            } elseif (str_starts_with((string) $order->snap_token, 'MOCK-SNAP-')) {
                $order->update(['snap_token' => null]);
            }

            $snapToken = $midtransService->createSnapToken($order);

            // Dispatch browser event to invoke Midtrans Snap popup
            $this->dispatch('open-midtrans-snap', [
                'token' => $snapToken,
                'orderCode' => $order->order_code,
                'workshopTitle' => $this->workshop->title,
            ]);
        } catch (Exception $e) {
            $this->errorMessage = 'Gagal memproses pembayaran: '.$e->getMessage();
        }
    }

    /**
     * Handle payment success callback from Frontend Midtrans Snap.
     */
    public function handlePaymentSuccess(string $orderCode): void
    {
        $order = Order::where('order_code', $orderCode)->first();

        if ($order) {
            if ($order->status !== 'paid') {
                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                Registration::firstOrCreate(
                    [
                        'user_id' => $order->user_id,
                        'workshop_id' => $order->workshop_id,
                    ],
                    [
                        'order_id' => $order->id,
                        'attendance_status' => 'not_attended',
                        'notes' => 'Lunas via Midtrans Snap Pop-up',
                    ]
                );

                if ($order->workshop && $order->workshop->remaining_quota > 0) {
                    $order->workshop->decrement('remaining_quota');
                }
            }

            session()->flash('success', 'Pembayaran berhasil! Tiket workshop Anda telah resmi terbit.');
            $this->redirectRoute('participant.tickets');
        }
    }

    public function render(): View
    {
        return view('livewire.pages.workshop-detail');
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Registration;
use App\Models\Workshop;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class QuickCheckIn extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationGroup = 'Transaksi & Pendaftaran';

    protected static ?string $navigationLabel = 'Presensi Cepat Tiket';

    protected static ?string $title = 'Presensi & Check-in Tiket Hari-H';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.quick-check-in';

    public string $ticketCode = '';

    public ?int $selectedWorkshopId = null;

    public ?array $lastCheckedIn = null;

    public ?string $scanStatus = null; // 'success', 'already_attended', 'not_found', 'error'

    public ?string $scanMessage = null;

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function mount(): void
    {
        // Default to the most recent published/ongoing workshop
        $latestWorkshop = Workshop::whereIn('status', ['published', 'ongoing'])
            ->orderBy('start_date', 'asc')
            ->first();

        $this->selectedWorkshopId = $latestWorkshop?->id;
    }

    /**
     * Process barcode or typed ticket check-in.
     */
    public function processCheckIn(): void
    {
        $code = trim(strtoupper($this->ticketCode));

        if (empty($code)) {
            Notification::make()
                ->title('Kode tiket tidak boleh kosong')
                ->warning()
                ->send();

            return;
        }

        $query = Registration::with(['user', 'workshop', 'order'])
            ->where('ticket_code', $code);

        if ($this->selectedWorkshopId) {
            $query->where('workshop_id', $this->selectedWorkshopId);
        }

        $registration = $query->first();

        if (! $registration) {
            $this->scanStatus = 'not_found';
            $this->scanMessage = "Tiket dengan kode '{$code}' tidak ditemukan pada event yang dipilih.";
            $this->lastCheckedIn = null;

            Notification::make()
                ->title('Tiket Tidak Valid!')
                ->body($this->scanMessage)
                ->danger()
                ->send();

            $this->ticketCode = '';

            return;
        }

        // Check if already attended
        if ($registration->isAttended()) {
            $this->scanStatus = 'already_attended';
            $attendedTime = $registration->attended_at ? $registration->attended_at->format('H:i:s, d M Y') : 'sebelumnya';
            $this->scanMessage = "Perhatian: Tiket sudah digunakan untuk check-in pada {$attendedTime}!";

            $this->lastCheckedIn = [
                'ticket_code' => $registration->ticket_code,
                'name' => $registration->user?->name,
                'email' => $registration->user?->email,
                'phone' => $registration->user?->phone_number,
                'institution' => $registration->user?->institution,
                'workshop' => $registration->workshop?->title,
                'attended_at' => $registration->attended_at?->format('d M Y, H:i:s'),
                'certificate_code' => $registration->certificate_code,
            ];

            Notification::make()
                ->title('Peringatan: Tiket Sudah Digunakan')
                ->body($this->scanMessage)
                ->warning()
                ->send();

            $this->ticketCode = '';

            return;
        }

        // Mark attended
        $registration->markAttended();

        $this->scanStatus = 'success';
        $this->scanMessage = "Presensi Berhasil: {$registration->user?->name} ({$registration->ticket_code}) telah tercatat hadir!";

        $this->lastCheckedIn = [
            'ticket_code' => $registration->ticket_code,
            'name' => $registration->user?->name,
            'email' => $registration->user?->email,
            'phone' => $registration->user?->phone_number,
            'institution' => $registration->user?->institution,
            'workshop' => $registration->workshop?->title,
            'attended_at' => now()->format('d M Y, H:i:s'),
            'certificate_code' => $registration->certificate_code,
        ];

        Notification::make()
            ->title('Check-in Berhasil!')
            ->body("Selamat datang, {$registration->user?->name}.")
            ->success()
            ->send();

        $this->ticketCode = '';
    }

    /**
     * Get statistics for selected workshop.
     *
     * @return array{total: int, attended: int, remaining: int, percentage: float}
     */
    public function getWorkshopStatsProperty(): array
    {
        $query = Registration::query();

        if ($this->selectedWorkshopId) {
            $query->where('workshop_id', $this->selectedWorkshopId);
        }

        $total = (clone $query)->count();
        $attended = (clone $query)->where('attendance_status', 'attended')->count();
        $remaining = max(0, $total - $attended);
        $percentage = $total > 0 ? round(($attended / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'attended' => $attended,
            'remaining' => $remaining,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get recent 5 check-ins.
     *
     * @return Collection<int, Registration>
     */
    public function getRecentCheckInsProperty(): Collection
    {
        $query = Registration::with(['user', 'workshop'])
            ->where('attendance_status', 'attended')
            ->orderBy('attended_at', 'desc');

        if ($this->selectedWorkshopId) {
            $query->where('workshop_id', $this->selectedWorkshopId);
        }

        return $query->limit(6)->get();
    }

    /**
     * Get available workshops for dropdown.
     *
     * @return Collection<int, Workshop>
     */
    public function getWorkshopsProperty(): Collection
    {
        return Workshop::orderBy('start_date', 'desc')->get();
    }
}

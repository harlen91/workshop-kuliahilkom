<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Registration;
use App\Models\Workshop;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::where('status', 'paid')->sum('amount');
        $totalRegistrations = Registration::count();
        $activeWorkshops = Workshop::where('status', 'published')->count();
        $attendedCount = Registration::where('attendance_status', 'attended')->count();
        $attendanceRate = $totalRegistrations > 0 ? round(($attendedCount / $totalRegistrations) * 100, 1) : 0;

        return [
            Stat::make('Total Pendapatan', 'Rp '.number_format($totalRevenue, 0, ',', '.'))
                ->description('Dari pembayaran workshop lunas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([25, 40, 60, 90, 110, 149]),

            Stat::make('Peserta Terdaftar', number_format($totalRegistrations, 0, ',', '.'))
                ->description('Total tiket event yang diterbitkan')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([5, 12, 19, 28, 35, 42]),

            Stat::make('Workshop Aktif', (string) $activeWorkshops)
                ->description('Event publik yang dapat didaftar')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Presensi Kehadiran', $attendanceRate.'%')
                ->description("{$attendedCount} peserta telah hadir")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning'),
        ];
    }
}

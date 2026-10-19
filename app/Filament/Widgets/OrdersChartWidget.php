<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan & Transaksi (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $dates = collect(range(6, 0))->map(function ($daysAgo) {
            return Carbon::today()->subDays($daysAgo)->format('Y-m-d');
        });

        $revenues = $dates->map(function ($date) {
            return (float) Order::where('status', 'paid')
                ->whereDate('paid_at', $date)
                ->sum('amount');
        });

        $orderCounts = $dates->map(function ($date) {
            return Order::whereDate('created_at', $date)->count();
        });

        $labels = $dates->map(function ($date) {
            return Carbon::parse($date)->isoFormat('dddd, D MMM');
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (IDR)',
                    'data' => $revenues->toArray(),
                    'borderColor' => '#4f46e5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $orderCounts->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b981',
                    'type' => 'bar',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

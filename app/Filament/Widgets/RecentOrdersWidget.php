<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Registration;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Transaksi Pembayaran Terbaru (Live Feed)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->with(['user', 'workshop'])->latest()->limit(6)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('No. Invoice')
                    ->weight('bold')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peserta')
                    ->description(fn (Order $record): string => $record->user?->email ?? '-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('workshop.title')
                    ->label('Event / Workshop')
                    ->limit(35),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state): string => 'Rp '.number_format((float) $state, 0, ',', '.'))
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'expired' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('payment_type')
                    ->label('Kanal Bayar')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state): string => strtoupper($state ?? 'ONLINE')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Order')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->status !== 'paid')
                    ->action(function (Order $record): void {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        Registration::firstOrCreate(
                            [
                                'user_id' => $record->user_id,
                                'workshop_id' => $record->workshop_id,
                            ],
                            [
                                'order_id' => $record->id,
                                'attendance_status' => 'not_attended',
                                'notes' => 'Diverifikasi manual oleh admin via dashboard widget',
                            ]
                        );

                        $workshop = $record->workshop;
                        if ($workshop && $workshop->remaining_quota > 0) {
                            $workshop->decrement('remaining_quota');
                        }

                        Notification::make()
                            ->title('Order berhasil dilunaskan & Tiket diterbitkan')
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated(false);
    }
}

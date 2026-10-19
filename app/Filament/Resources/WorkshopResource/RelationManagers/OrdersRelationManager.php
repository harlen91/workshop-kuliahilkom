<?php

namespace App\Filament\Resources\WorkshopResource\RelationManagers;

use App\Models\Order;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Riwayat Transaksi & Pembayaran';

    protected static ?string $icon = 'heroicon-o-credit-card';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_code')
                    ->label('Nomor Invoice')
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Nominal')
                    ->prefix('Rp')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'paid' => 'Paid (Lunas)',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('payment_type')
                    ->label('Kanal Pembayaran'),
                Forms\Components\DateTimePicker::make('paid_at')
                    ->label('Waktu Lunas'),
            ]);
    }

    public function table(Table $table): Table
    {
        $workshopTitle = $this->getOwnerRecord()->title;

        return $table
            ->recordTitleAttribute('order_code')
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('No. Invoice')
                    ->weight('bold')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peserta')
                    ->description(fn (Order $record): string => $record->user?->email ?? '-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state): string => 'Rp '.number_format((float) $state, 0, ',', '.'))
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Waktu Bayar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'paid' => 'Paid (Lunas)',
                        'pending' => 'Pending',
                        'expired' => 'Expired',
                        'failed' => 'Failed',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('Export Keuangan Workshop (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () use ($workshopTitle): StreamedResponse {
                        $orders = $this->getOwnerRecord()->orders()->with('user')->get();

                        return response()->streamDownload(function () use ($orders): void {
                            $handle = fopen('php://output', 'w');
                            fwrite($handle, "\xEF\xBB\xBF");
                            fputcsv($handle, ['No', 'No. Invoice', 'Nama Peserta', 'Email', 'No. WhatsApp', 'Nominal (IDR)', 'Status', 'Metode Pembayaran', 'Waktu Lunas', 'Tanggal Order']);

                            $no = 1;
                            foreach ($orders as $order) {
                                fputcsv($handle, [
                                    $no++,
                                    $order->order_code,
                                    $order->user?->name ?? '-',
                                    $order->user?->email ?? '-',
                                    $order->user?->phone_number ?? '-',
                                    $order->amount,
                                    strtoupper($order->status),
                                    $order->payment_type ?? 'ONLINE',
                                    $order->paid_at ? $order->paid_at->format('Y-m-d H:i:s') : '-',
                                    $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '-',
                                ]);
                            }
                            fclose($handle);
                        }, 'transaksi-'.str($workshopTitle)->slug().'-'.now()->format('YmdHis').'.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label('Lunas')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Order $record): bool => $record->status !== 'paid')
                    ->requiresConfirmation()
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
                                'notes' => 'Diverifikasi manual oleh admin via workshop relation',
                            ]
                        );

                        $workshop = $record->workshop;
                        if ($workshop && $workshop->remaining_quota > 0) {
                            $workshop->decrement('remaining_quota');
                        }

                        Notification::make()
                            ->title('Order dilunaskan & Tiket diterbitkan')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }
}

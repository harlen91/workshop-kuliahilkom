<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Transaksi & Pendaftaran';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Transaksi Pembayaran';

    protected static ?string $pluralModelLabel = 'Daftar Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Invoice & Pembayaran')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('order_code')
                                ->label('Nomor Invoice')
                                ->required()
                                ->readOnly(),
                            Forms\Components\Select::make('user_id')
                                ->label('Peserta')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->required(),
                            Forms\Components\Select::make('workshop_id')
                                ->label('Workshop / Webinar')
                                ->relationship('workshop', 'title')
                                ->searchable()
                                ->required(),
                        ]),
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('amount')
                                ->label('Total Pembayaran (IDR)')
                                ->prefix('Rp')
                                ->numeric()
                                ->required(),
                            Forms\Components\Select::make('status')
                                ->label('Status Transaksi')
                                ->options([
                                    'pending' => 'Pending (Menunggu Bayar)',
                                    'paid' => 'Paid (Lunas)',
                                    'failed' => 'Failed (Gagal)',
                                    'expired' => 'Expired (Kedaluwarsa)',
                                    'cancelled' => 'Cancelled (Dibatalkan)',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('payment_type')
                                ->label('Metode Pembayaran')
                                ->placeholder('qris / bank_transfer / gopay'),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('midtrans_transaction_id')
                                ->label('Midtrans Transaction ID'),
                            Forms\Components\DateTimePicker::make('paid_at')
                                ->label('Waktu Pembayaran'),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('No. Invoice')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peserta')
                    ->description(fn (Order $record): string => $record->user?->email ?? '-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('workshop.title')
                    ->label('Workshop')
                    ->limit(35)
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
                    ->label('Waktu Lunas')
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

                        // Generate or activate registration ticket
                        Registration::firstOrCreate(
                            [
                                'user_id' => $record->user_id,
                                'workshop_id' => $record->workshop_id,
                            ],
                            [
                                'order_id' => $record->id,
                                'attendance_status' => 'not_attended',
                                'notes' => 'Diverifikasi manual oleh admin',
                            ]
                        );

                        // Decrement workshop quota if set
                        $workshop = $record->workshop;
                        if ($workshop && $workshop->remaining_quota > 0) {
                            $workshop->decrement('remaining_quota');
                        }

                        Notification::make()
                            ->title('Order berhasil dilunaskan & Tiket diterbitkan')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

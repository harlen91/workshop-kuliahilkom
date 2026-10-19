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
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Transaksi & Pendaftaran';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Transaksi Pembayaran';

    protected static ?string $pluralModelLabel = 'Daftar Transaksi';

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = Order::where('status', 'pending')->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Menunggu konfirmasi pembayaran';
    }

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
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('Export Rekap Keuangan (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (): StreamedResponse {
                        $orders = Order::with(['user', 'workshop'])->orderBy('id', 'desc')->get();

                        return response()->streamDownload(function () use ($orders): void {
                            $handle = fopen('php://output', 'w');
                            fwrite($handle, "\xEF\xBB\xBF");
                            fputcsv($handle, ['No', 'No. Invoice', 'Nama Peserta', 'Email', 'No. WhatsApp', 'Event / Workshop', 'Nominal (IDR)', 'Status', 'Metode Pembayaran', 'Waktu Lunas', 'Tanggal Order']);

                            $no = 1;
                            foreach ($orders as $order) {
                                fputcsv($handle, [
                                    $no++,
                                    $order->order_code,
                                    $order->user?->name ?? '-',
                                    $order->user?->email ?? '-',
                                    $order->user?->phone_number ?? '-',
                                    $order->workshop?->title ?? '-',
                                    $order->amount,
                                    strtoupper($order->status),
                                    $order->payment_type ?? 'ONLINE',
                                    $order->paid_at ? $order->paid_at->format('Y-m-d H:i:s') : '-',
                                    $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '-',
                                ]);
                            }
                            fclose($handle);
                        }, 'laporan-keuangan-transaksi-'.now()->format('YmdHis').'.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->modalHeading(fn (Order $record): string => "Invoice Pembayaran: {$record->order_code}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Order $record) => view('filament.modals.order-invoice-preview', [
                        'record' => $record,
                    ])),
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
                    Tables\Actions\BulkAction::make('bulk_export')
                        ->label('Export Terpilih (CSV)')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->action(function (Collection $records): StreamedResponse {
                            return response()->streamDownload(function () use ($records): void {
                                $handle = fopen('php://output', 'w');
                                fwrite($handle, "\xEF\xBB\xBF");
                                fputcsv($handle, ['No', 'No. Invoice', 'Nama Peserta', 'Email', 'No. WhatsApp', 'Event / Workshop', 'Nominal (IDR)', 'Status', 'Metode Pembayaran', 'Waktu Lunas']);

                                $no = 1;
                                foreach ($records as $order) {
                                    fputcsv($handle, [
                                        $no++,
                                        $order->order_code,
                                        $order->user?->name ?? '-',
                                        $order->user?->email ?? '-',
                                        $order->user?->phone_number ?? '-',
                                        $order->workshop?->title ?? '-',
                                        $order->amount,
                                        strtoupper($order->status),
                                        $order->payment_type ?? 'ONLINE',
                                        $order->paid_at ? $order->paid_at->format('Y-m-d H:i:s') : '-',
                                    ]);
                                }
                                fclose($handle);
                            }, 'export-transaksi-terpilih-'.now()->format('YmdHis').'.csv', [
                                'Content-Type' => 'text/csv; charset=UTF-8',
                            ]);
                        }),
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

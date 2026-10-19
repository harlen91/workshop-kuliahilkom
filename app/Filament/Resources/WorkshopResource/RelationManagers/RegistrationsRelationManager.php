<?php

namespace App\Filament\Resources\WorkshopResource\RelationManagers;

use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Peserta Terdaftar & Presensi';

    protected static ?string $icon = 'heroicon-o-ticket';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ticket_code')
                    ->label('Kode Tiket')
                    ->readOnly()
                    ->required(),
                Forms\Components\Select::make('attendance_status')
                    ->label('Status Presensi')
                    ->options([
                        'not_attended' => 'Belum Hadir',
                        'attended' => 'Hadir',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('attended_at')
                    ->label('Waktu Presensi'),
                Forms\Components\TextInput::make('certificate_code')
                    ->label('Nomor Sertifikat'),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        $workshopTitle = $this->getOwnerRecord()->title;

        return $table
            ->recordTitleAttribute('ticket_code')
            ->columns([
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Kode Tiket')
                    ->weight('bold')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Peserta')
                    ->description(fn (Registration $record): string => $record->user?->email ?? '-')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.phone_number')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->formatStateUsing(fn (?string $state): string => $state ?: '-'),
                Tables\Columns\TextColumn::make('attendance_status')
                    ->label('Presensi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'attended' => 'success',
                        'not_attended' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'attended' => 'Hadir',
                        'not_attended' => 'Belum Hadir',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('attended_at')
                    ->label('Waktu Hadir')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('certificate_code')
                    ->label('No. Sertifikat')
                    ->copyable()
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('attendance_status')
                    ->label('Status Kehadiran')
                    ->options([
                        'attended' => 'Hadir',
                        'not_attended' => 'Belum Hadir',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('Export Daftar Hadir (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () use ($workshopTitle): StreamedResponse {
                        $registrations = $this->getOwnerRecord()->registrations()->with('user')->get();

                        return response()->streamDownload(function () use ($registrations): void {
                            $handle = fopen('php://output', 'w');
                            // Add UTF-8 BOM for Excel compatibility
                            fwrite($handle, "\xEF\xBB\xBF");
                            fputcsv($handle, ['No', 'Kode Tiket', 'Nama Peserta', 'Email', 'No. WhatsApp', 'Institusi', 'Status Presensi', 'Waktu Presensi', 'No. Sertifikat', 'Tanggal Terdaftar']);

                            $no = 1;
                            foreach ($registrations as $reg) {
                                fputcsv($handle, [
                                    $no++,
                                    $reg->ticket_code,
                                    $reg->user?->name ?? '-',
                                    $reg->user?->email ?? '-',
                                    $reg->user?->phone_number ?? '-',
                                    $reg->user?->institution ?? '-',
                                    $reg->attendance_status === 'attended' ? 'Hadir' : 'Belum Hadir',
                                    $reg->attended_at ? $reg->attended_at->format('Y-m-d H:i:s') : '-',
                                    $reg->certificate_code ?? '-',
                                    $reg->created_at ? $reg->created_at->format('Y-m-d H:i:s') : '-',
                                ]);
                            }
                            fclose($handle);
                        }, 'peserta-'.str($workshopTitle)->slug().'-'.now()->format('YmdHis').'.csv', [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_attended')
                    ->label('Hadir')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Registration $record): bool => $record->attendance_status !== 'attended')
                    ->requiresConfirmation()
                    ->action(function (Registration $record): void {
                        $record->update([
                            'attendance_status' => 'attended',
                            'attended_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Presensi berhasil dicatat')
                            ->body("Peserta {$record->user?->name} ditandai hadir.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('whatsapp')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->visible(fn (Registration $record): bool => ! empty($record->user?->phone_number))
                    ->url(function (Registration $record): string {
                        $phone = preg_replace('/[^0-9]/', '', (string) $record->user->phone_number);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62'.substr($phone, 1);
                        }
                        $workshop = $record->workshop;
                        $text = "Halo {$record->user->name}, tiket workshop '{$workshop->title}' Anda aktif dengan Kode: {$record->ticket_code}. Jangan lupa hadir ya!";

                        return "https://wa.me/{$phone}?text=".urlencode($text);
                    }, shouldOpenInNewTab: true),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_attended')
                        ->label('Tandai Hadir Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(fn (Registration $r) => $r->update([
                                'attendance_status' => 'attended',
                                'attended_at' => now(),
                            ]));
                            Notification::make()
                                ->title(count($records).' peserta berhasil ditandai hadir')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('bulk_certificate')
                        ->label('Generate Nomor Sertifikat Terpilih')
                        ->icon('heroicon-o-academic-cap')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $count = 0;
                            foreach ($records as $record) {
                                if (empty($record->certificate_code)) {
                                    $certNumber = 'CERT/ILKOM/'.now()->format('Ym').'/'.str_pad((string) $record->id, 5, '0', STR_PAD_LEFT);
                                    $record->update(['certificate_code' => $certNumber]);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} Nomor sertifikat berhasil diterbitkan")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

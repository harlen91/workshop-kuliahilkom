<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Transaksi & Pendaftaran';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Tiket & Presensi Peserta';

    protected static ?string $pluralModelLabel = 'Daftar Tiket Peserta';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Tiket Peserta')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('ticket_code')
                                ->label('Nomor Tiket')
                                ->readOnly()
                                ->required(),
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
                            Forms\Components\Select::make('attendance_status')
                                ->label('Status Kehadiran')
                                ->options([
                                    'not_attended' => 'Belum Hadir',
                                    'attended' => 'Sudah Hadir (Presensi OK)',
                                ])
                                ->required(),
                            Forms\Components\DateTimePicker::make('attended_at')
                                ->label('Waktu Presensi'),
                            Forms\Components\TextInput::make('certificate_code')
                                ->label('Nomor Sertifikat'),
                        ]),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Kode Tiket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Peserta')
                    ->description(fn (Registration $record): string => ($record->user?->phone_number ? $record->user->phone_number.' • ' : '').($record->user?->email ?? ''))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('workshop.title')
                    ->label('Event')
                    ->limit(35)
                    ->searchable()
                    ->sortable(),
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
                    ->label('Waktu Check-in')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('certificate_code')
                    ->label('No. Sertifikat')
                    ->badge()
                    ->color('primary')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Daftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('workshop')
                    ->label('Filter Event')
                    ->relationship('workshop', 'title'),
                Tables\Filters\SelectFilter::make('attendance_status')
                    ->label('Status Kehadiran')
                    ->options([
                        'attended' => 'Sudah Hadir',
                        'not_attended' => 'Belum Hadir',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('checkIn')
                    ->label('Check-In')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Registration $record): bool => ! $record->isAttended())
                    ->action(function (Registration $record): void {
                        $record->markAttended();

                        Notification::make()
                            ->title('Peserta berhasil di-check-in & Nomor Sertifikat dibuat')
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
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Daftar Pengguna';

    public static function getNavigationBadge(): ?string
    {
        return (string) User::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun & Akses')
                    ->description('Kelola identitas akun dan hak akses pengguna di sistem.')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->label('Alamat Email')
                                ->email()
                                ->required()
                                ->unique(User::class, 'email', ignoreRecord: true)
                                ->maxLength(255),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('role')
                                ->label('Peran / Hak Akses (Role)')
                                ->options([
                                    'admin' => 'Administrator (Akses Penuh Admin Panel)',
                                    'participant' => 'Peserta (Akses Portal Peserta Saja)',
                                ])
                                ->default('participant')
                                ->required(),
                            Forms\Components\TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable()
                                ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Kosongkan jika tidak ingin mengubah password saat ini.' : null),
                        ]),
                    ]),

                Forms\Components\Section::make('Profil & Kontak Peserta')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('phone_number')
                                ->label('Nomor WhatsApp / HP')
                                ->tel()
                                ->placeholder('Contoh: 081234567890')
                                ->maxLength(20),
                            Forms\Components\TextInput::make('institution')
                                ->label('Institusi / Universitas / Perusahaan')
                                ->placeholder('Contoh: Universitas Indonesia / Tokopedia')
                                ->maxLength(255),
                        ]),
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Foto Profil (Avatar)')
                            ->disk('public')
                            ->directory('avatars')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(2048)
                            ->helperText('Format foto JPG/PNG/WEBP, Maks. 2MB.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => $record->avatar_url),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (User $record): string => $record->email),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'participant' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->formatStateUsing(fn (?string $state): string => $state ?: '-'),
                Tables\Columns\TextColumn::make('institution')
                    ->label('Institusi')
                    ->limit(25)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Tiket Event')
                    ->counts('registrations')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Transaksi')
                    ->counts('orders')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter Role')
                    ->options([
                        'admin' => 'Administrator',
                        'participant' => 'Peserta',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (User $record): bool => ! empty($record->phone_number))
                    ->url(function (User $record): string {
                        $phone = preg_replace('/[^0-9]/', '', (string) $record->phone_number);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62'.substr($phone, 1);
                        }

                        return "https://wa.me/{$phone}?text=".urlencode("Halo {$record->name}, salam dari Admin KuliahIlkom.");
                    }, shouldOpenInNewTab: true),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, User $record): void {
                        if (auth()->id() === $record->id) {
                            Notification::make()
                                ->title('Tidak dapat menghapus akun Anda sendiri')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

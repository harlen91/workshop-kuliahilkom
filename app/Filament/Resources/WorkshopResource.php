<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkshopResource\Pages;
use App\Models\Workshop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class WorkshopResource extends Resource
{
    protected static ?string $model = Workshop::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Manajemen Event';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Workshop & Webinar';

    protected static ?string $pluralModelLabel = 'Daftar Workshop & Webinar';

    public static function getNavigationBadge(): ?string
    {
        return (string) Workshop::whereIn('status', ['published', 'ongoing'])->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Detail Workshop')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informasi Utama')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('Judul Event')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (string $operation, $state, Forms\Set $set): void {
                                            if ($operation === 'create') {
                                                $set('slug', Str::slug($state));
                                            }
                                        }),
                                    Forms\Components\TextInput::make('slug')
                                        ->label('Slug URL')
                                        ->required()
                                        ->unique(Workshop::class, 'slug', ignoreRecord: true)
                                        ->maxLength(255),
                                ]),
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\Select::make('category_id')
                                        ->label('Kategori')
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                    Forms\Components\Select::make('speaker_id')
                                        ->label('Instruktur / Pembicara')
                                        ->relationship('speaker', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                    Forms\Components\Select::make('type')
                                        ->label('Tipe Event')
                                        ->options([
                                            'webinar' => 'Webinar Online',
                                            'workshop' => 'Workshop Hands-on',
                                            'bootcamp' => 'Bootcamp Intensif',
                                        ])
                                        ->default('webinar')
                                        ->required(),
                                ]),
                                Forms\Components\Textarea::make('short_description')
                                    ->label('Deskripsi Singkat')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('Akan ditampilkan pada katalog kartu event di halaman depan.'),
                                Forms\Components\RichEditor::make('description')
                                    ->label('Rincian Lengkap / Silabus')
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('banner_path')
                                    ->label('Upload Banner Workshop / Webinar')
                                    ->disk('public')
                                    ->directory('workshops/banners')
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1280')
                                    ->imageResizeTargetHeight('720')
                                    ->maxSize(5120)
                                    ->helperText('Format: JPG, PNG, WEBP (Rasio ideal 16:9, Maks. 5MB). Gambar akan disimpan otomatis di storage link.')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Jadwal & Platform')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\DateTimePicker::make('start_date')
                                        ->label('Waktu Mulai')
                                        ->seconds(false)
                                        ->required(),
                                    Forms\Components\DateTimePicker::make('end_date')
                                        ->label('Waktu Selesai')
                                        ->seconds(false)
                                        ->required(),
                                    Forms\Components\TextInput::make('timezone')
                                        ->label('Zona Waktu')
                                        ->default('WIB')
                                        ->required(),
                                ]),
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\Select::make('platform')
                                        ->label('Platform Pelaksanaan')
                                        ->options([
                                            'zoom' => 'Zoom Meeting',
                                            'gmeet' => 'Google Meet',
                                            'youtube' => 'YouTube Live (Unlisted)',
                                            'offline' => 'Tatap Muka (Offline)',
                                        ])
                                        ->default('zoom')
                                        ->required(),
                                    Forms\Components\TextInput::make('meeting_url')
                                        ->label('Link Akses / URL Meeting')
                                        ->placeholder('https://meet.google.com/... atau https://zoom.us/j/...')
                                        ->helperText('Link ruang meeting virtual. Sistem otomatis menambahkan https:// jika tidak disertakan.'),
                                    Forms\Components\TextInput::make('meeting_password')
                                        ->label('Passcode / Password Meeting')
                                        ->helperText('Password untuk join room (opsional).'),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Tiket, Harga & Kuota')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Toggle::make('is_paid')
                                        ->label('Event Berbayar')
                                        ->live()
                                        ->default(false),
                                    Forms\Components\Select::make('status')
                                        ->label('Status Publikasi')
                                        ->options([
                                            'draft' => 'Draft (Tidak Terlihat)',
                                            'published' => 'Published (Tampil di Katalog)',
                                            'ongoing' => 'Sedang Berlangsung',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                        ])
                                        ->default('published')
                                        ->required(),
                                ]),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('price')
                                        ->label('Harga Tiket (IDR)')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->default(0)
                                        ->disabled(fn (Forms\Get $get): bool => ! $get('is_paid')),
                                    Forms\Components\TextInput::make('original_price')
                                        ->label('Harga Coret / Normal (IDR)')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->helperText('Untuk menampilkan diskon (strikethrough) di halaman katalog.'),
                                ]),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('quota')
                                        ->label('Total Kuota Peserta')
                                        ->numeric()
                                        ->helperText('Kosongkan jika kuota tidak terbatas.')
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set): void {
                                            if ($get('remaining_quota') === null) {
                                                $set('remaining_quota', $state);
                                            }
                                        }),
                                    Forms\Components\TextInput::make('remaining_quota')
                                        ->label('Sisa Kuota Saat Ini')
                                        ->numeric(),
                                ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Fasilitas & Pasca Acara')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Forms\Components\Toggle::make('certificate_enabled')
                                    ->label('Menyediakan E-Sertifikat')
                                    ->default(true),
                                Forms\Components\TextInput::make('recording_url')
                                    ->label('Link Rekaman Video')
                                    ->placeholder('https://youtube.com/watch?v=...')
                                    ->helperText('Dapat diakses peserta setelah acara selesai.'),
                                Forms\Components\TextInput::make('materials_url')
                                    ->label('Link Materi / Slide / Source Code')
                                    ->placeholder('https://github.com/... atau Google Drive')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner_path')
                    ->label('Banner')
                    ->disk('public')
                    ->circular(false)
                    ->width(70)
                    ->height(40)
                    ->defaultImageUrl('https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=400&q=80'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->label('Instruktur')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'webinar' => 'info',
                        'workshop' => 'primary',
                        'bootcamp' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Jadwal Mulai')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn ($record): string => $record->formattedPrice())
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_quota')
                    ->label('Sisa Kuota')
                    ->formatStateUsing(fn ($record): string => $record->quota ? "{$record->remaining_quota} / {$record->quota}" : 'Unlimited')
                    ->badge()
                    ->color(fn ($record): string => ($record->quota && $record->remaining_quota <= 5) ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'ongoing' => 'info',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Filter Kategori'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Filter Tipe')
                    ->options([
                        'webinar' => 'Webinar',
                        'workshop' => 'Workshop',
                        'bootcamp' => 'Bootcamp',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp_broadcast')
                    ->label('Broadcast WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->modalHeading(fn (Workshop $record): string => "Broadcast WhatsApp Peserta: {$record->title}")
                    ->modalDescription('Salin daftar nomor WhatsApp peserta yang terdaftar untuk blast pesan reminder / materi.')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(function (Workshop $record) {
                        $contacts = $record->registrations()->with('user')->get()->map(function ($reg) {
                            return ($reg->user?->name ?? 'Peserta').' - '.($reg->user?->phone_number ?? 'No HP Kosong');
                        })->join("\n");

                        $phoneList = $record->registrations()->with('user')->get()->map(function ($reg) {
                            $phone = preg_replace('/[^0-9]/', '', (string) ($reg->user?->phone_number ?? ''));
                            if (str_starts_with($phone, '0')) {
                                $phone = '62'.substr($phone, 1);
                            }

                            return $phone;
                        })->filter()->unique()->join(',');

                        return view('filament.modals.workshop-broadcast-info', [
                            'record' => $record,
                            'contacts' => $contacts,
                            'phoneList' => $phoneList,
                        ]);
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
            WorkshopResource\RelationManagers\RegistrationsRelationManager::class,
            WorkshopResource\RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkshops::route('/'),
            'create' => Pages\CreateWorkshop::route('/create'),
            'edit' => Pages\EditWorkshop::route('/{record}/edit'),
        ];
    }
}

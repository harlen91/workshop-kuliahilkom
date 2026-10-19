<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpeakerResource\Pages;
use App\Models\Speaker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SpeakerResource extends Resource
{
    protected static ?string $model = Speaker::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Instruktur / Pembicara';

    protected static ?string $pluralModelLabel = 'Pembicara';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Biodata Pembicara')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Lengkap & Gelar')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('title_role')
                                ->label('Jabatan / Keahlian')
                                ->placeholder('Senior Software Engineer / AI Researcher')
                                ->required()
                                ->maxLength(255),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('institution')
                                ->label('Perusahaan / Kampus')
                                ->placeholder('Google / Tokopedia / ITB')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('linkedin_url')
                                ->label('Profil LinkedIn')
                                ->placeholder('https://linkedin.com/in/...'),
                        ]),
                        Forms\Components\FileUpload::make('photo_url')
                            ->label('Upload Foto Profil Pembicara')
                            ->disk('public')
                            ->directory('speakers/photos')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(3072)
                            ->helperText('Format: JPG, PNG, WEBP (Pasfoto kotak/lingkaran, Maks. 3MB). Tersimpan di storage link.')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('bio')
                            ->label('Biografi Singkat')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_url')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=Speaker&background=4f46e5&color=fff'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('title_role')
                    ->label('Jabatan / Peran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('institution')
                    ->label('Institusi')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('workshops_count')
                    ->label('Workshop Diajar')
                    ->counts('workshops')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSpeakers::route('/'),
            'create' => Pages\CreateSpeaker::route('/create'),
            'edit' => Pages\EditSpeaker::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // Basic Information Section
                Section::make('Informasi Dasar')
                    ->description('Informasi utama kampanye')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Gambar Utama Kampanye')
                                    ->image()
                                    ->maxSize(5120)
                                    ->columnSpanFull(),

                                TextInput::make('title')
                                    ->label('Judul Kampanye')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),

                                TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                            ]),

                        RichEditor::make('description')
                            ->label('Deskripsi Kampanye')
                            ->required()
                            ->columnSpanFull()
                    ])
                    ->columnSpanFull(),

                // Campaign Details Section
                Section::make('Detail Kampanye')
                    ->description('Pengaturan spesifik kampanye')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('campaign_category_id')
                                    ->label('Kategori Kampanye')
                                    ->relationship('category', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nama Kategori')
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        return \App\Models\CampaignCategory::create($data)->getKey();
                                    }),

                                TextInput::make('target_amount')
                                    ->label('Target Dana')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->step(1000),

                            ]),

                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->seconds(false),

                                DateTimePicker::make('end_date')
                                    ->label('Tanggal Berakhir')
                                    ->required()
                                    ->minDate(fn (callable $get) => $get('start_date'))
                                    ->seconds(false),
                            ]),

                        Toggle::make('is_featured')
                            ->label('Kampanye Unggulan')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),



                // Campaign Status Section
                Section::make('Status Kampanye')
                    ->description('Pengaturan status dan visibilitas')
                    ->icon('heroicon-o-flag')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status Kampanye')
                                    ->required()
                                    ->options([
                                        0 => 'Draft',
                                        1 => 'Aktif',
                                        2 => 'Paused',
                                        3 => 'Completed',
                                        4 => 'Cancelled',
                                    ])
                                    ->default(0),

                                Select::make('user_id')
                                    ->label('Pemilik Kampanye')
                                    ->relationship('user', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

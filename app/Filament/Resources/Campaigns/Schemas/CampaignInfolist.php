<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\CampaignCategory;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\SelectAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\SelectColumn;


class CampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Section::make('Primary Information')
                    ->headerActions([
                        self::updateStatus(),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->description('Primary information about the campaign')
                    ->columnSpan(3)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul Kampanye')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('slug')
                            ->label('Slug'),
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        ImageEntry::make('image')
                            ->label('Gambar Kampanye'),
                        TextEntry::make('campaign_category_id')
                            ->label('Kategori')
                            ->formatStateUsing(fn($state) => CampaignCategory::find($state)?->name ?? 'Tidak ada kategori'),
                        TextEntry::make('user_id')
                            ->label('Pemilik')
                            ->formatStateUsing(fn($state) => User::find($state)?->name ?? 'Tidak ada pemilik'),
                        TextEntry::make('target_amount')
                            ->label('Target Dana')
                            ->money('IDR'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                '0' => 'gray',
                                '1' => 'success',
                                '2' => 'warning',
                                '3' => 'info',
                                '4' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                '0' => 'Draft',
                                '1' => 'Aktif',
                                '2' => 'Paused',
                                '3' => 'Completed',
                                '4' => 'Cancelled',
                                default => 'Unknown',
                            }),
                        IconEntry::make('is_featured')
                            ->label('Unggulan')
                            ->boolean(),
                    ]),
                Section::make('Time')
                    ->columnSpan(1)
                    ->icon('heroicon-o-clock')
                    ->description('Time information about the campaign')
                    ->schema([
                        TextEntry::make('start_date')
                            ->label('Tanggal Mulai')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('end_date')
                            ->label('Tanggal Berakhir')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d M Y H:i')
                    ]),
            ]);
    }

    private static function updateStatus()
    {
        return Action::make('updateStatus')
            ->modalHeading('Update Status')
            ->modalDescription('Update the status of the campaign')
            ->modalWidth(Width::Small)
            ->schema([
                Select::make('status')
                    ->label('Status')
                    ->options([
                        '0' => 'Draft',
                        '1' => 'Aktif',
                        '2' => 'Paused',
                        '3' => 'Completed',
                        '4' => 'Cancelled',
                    ])
                    ->required()
            ])->action(function (array $data, $record) {
                $record->status = $data['status'];
                $record->save();
                Notification::make('Status updated successfully')
                    ->success()
                    ->send();
            });
    }
}

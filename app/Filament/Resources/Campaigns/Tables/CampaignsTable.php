<?php

namespace App\Filament\Resources\Campaigns\Tables;

use App\Helper\NumberHelper;
use App\Models\Campaign;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                ImageColumn::make('image')
                    ->label('Thumbnail')
                    ->circular(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->description(function (Campaign $record): string {
                        $category = $record->category->name ?? 'Tanpa kategori';
                        $featured = $record->is_featured ? 'Unggulan' : 'Regular';

                        return $category.' - '.$featured;
                    }),
                TextColumn::make('target_amount')
                    ->label('Target Dana')
                    ->money('IDR')
                    ->description(function (Campaign $record): string {
                        $percentage = $record->target_amount > 0 ? ($record->total_donations / $record->target_amount) * 100 : 0;
                        return NumberHelper::formatIDR($record->total_donations) . ' - ' . number_format($percentage, 2) . '%';
                    })
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'gray',
                        '1' => 'success',
                        '2' => 'warning',
                        '3' => 'info',
                        '4' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '0' => 'Draft',
                        '1' => 'Aktif',
                        '2' => 'Paused',
                        '3' => 'Completed',
                        '4' => 'Cancelled',
                        default => 'Unknown',
                    })
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kampanye')
                    ->options([
                        '' => 'Semua Status',
                        0 => 'Draft',
                        1 => 'Aktif',
                        2 => 'Paused',
                        3 => 'Completed',
                        4 => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('donations')
                        ->label('Lihat Donasi')
                        ->icon('heroicon-o-heart')
                        ->color('success')
                        ->url(fn (Campaign $record): string => route('filament.admin.resources.campaigns.view', ['record' => $record]).'?relation=0'),

                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Donations\Tables;

use App\Filament\Resources\Donations\Helpers\DonationHelper;
use App\Models\Donation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Donor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('IDR')
                    ->description(function ($record) {
                        return " {$record->order_id} | {$record->payment_method}";
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->formatStateUsing(function ($state) {
                        return DonationHelper::getDonationStatusLabel($state);
                    })
                    ->badge()
                    ->color(function ($state) {
                        return DonationHelper::getDonationStatusColor($state);
                    })
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(self::getFilters(), layout: FiltersLayout::AboveContent )
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function getFilters()
    {
        return [
            SelectFilter::make('user_id')
                ->relationship('user', 'name')
                ->label('Donor')
                ->preload()
                ->searchable(),
            SelectFilter::make('status')
                ->options([
                    Donation::STATUS_CANCELLED => 'Cancelled',
                    Donation::STATUS_FAILED => 'Failed',
                    Donation::STATUS_PAID => 'Paid',
                    Donation::STATUS_PENDING => 'Pending',
                ])
                ->label('Status'),
        ];

    }
}

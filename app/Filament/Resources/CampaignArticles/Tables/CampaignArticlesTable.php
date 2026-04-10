<?php

namespace App\Filament\Resources\CampaignArticles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CampaignArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author.name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return ucfirst($state);
                    })
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->label('Status')
                    ->placeholder('All Statuses')
                    ->searchable(),
                SelectFilter::make('campaign_id')
                    ->relationship('campaign', 'title')
                    ->label('Campaign')
                    ->placeholder('All Campaigns')
                    ->searchable(),
                SelectFilter::make('author_id')
                    ->relationship('author', 'name')
                    ->label('Author')
                    ->placeholder('All Authors')
                    ->searchable(),

            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Filament\Resources\Campaigns\Resources\Donations\DonationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class DonationsRelationManager extends RelationManager
{
    protected static string $relationship = 'donations';

    protected static ?string $relatedResource = DonationResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

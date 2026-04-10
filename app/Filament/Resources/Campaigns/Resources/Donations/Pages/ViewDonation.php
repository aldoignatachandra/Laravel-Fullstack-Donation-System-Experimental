<?php

namespace App\Filament\Resources\Campaigns\Resources\Donations\Pages;

use App\Filament\Resources\Campaigns\Resources\Donations\DonationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDonation extends ViewRecord
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

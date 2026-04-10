<?php

namespace App\Filament\Resources\Campaigns\Resources\Donations\Pages;

use App\Filament\Resources\Campaigns\Resources\Donations\DonationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonation extends CreateRecord
{
    protected static string $resource = DonationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['campaign_id'] = $this->getOwnerRecord()->id;

        return $data;
    }
}

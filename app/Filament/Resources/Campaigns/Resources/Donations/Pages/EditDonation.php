<?php

namespace App\Filament\Resources\Campaigns\Resources\Donations\Pages;

use App\Filament\Resources\Campaigns\Resources\Donations\DonationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDonation extends EditRecord
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['campaign_id'] = $this->getOwnerRecord()->id;

        return $data;
    }
}

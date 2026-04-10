<?php

namespace App\Filament\Resources\CampaignCategories\Pages;

use App\Filament\Resources\CampaignCategories\CampaignCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaignCategory extends ViewRecord
{
    protected static string $resource = CampaignCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

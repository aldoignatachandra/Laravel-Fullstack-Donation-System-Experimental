<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;
    
    /**
     * @return string|null
     */
    public function getHeading(): \Illuminate\Contracts\Support\Htmlable|string
    {
        return '#' . $this->record->id . ' - ' . $this->record->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

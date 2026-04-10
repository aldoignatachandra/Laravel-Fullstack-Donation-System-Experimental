<?php

namespace App\Filament\Resources\CampaignArticles\Pages;

use App\Filament\Resources\CampaignArticles\CampaignArticleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampaignArticles extends ListRecords
{
    protected static string $resource = CampaignArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

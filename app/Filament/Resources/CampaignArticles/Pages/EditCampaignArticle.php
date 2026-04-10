<?php

namespace App\Filament\Resources\CampaignArticles\Pages;

use App\Filament\Resources\CampaignArticles\CampaignArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignArticle extends EditRecord
{
    protected static string $resource = CampaignArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

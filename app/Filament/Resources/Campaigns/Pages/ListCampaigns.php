<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Filament\Resources\Campaigns\Widgets\CampaignsByStatusWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $model = $this->getModel();

        return [
            'all' => Tab::make('All')
                ->badge($model::query()->count()),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 0))
                ->badge($model::query()->where('status', 0)->count()),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 1))
                ->badge($model::query()->where('status', 1)->count()),
            'paused' => Tab::make('Paused')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 2))
                ->badge($model::query()->where('status', 2)->count()),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 3))
                ->badge($model::query()->where('status', 3)->count()),
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 4))
                ->badge($model::query()->where('status', 4)->count()),
        ];
    }
    
}

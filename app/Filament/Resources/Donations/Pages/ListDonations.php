<?php

namespace App\Filament\Resources\Donations\Pages;

use App\Filament\Resources\Donations\DonationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDonations extends ListRecords
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $model = $this->getModel();

        return [
            'all' => Tab::make('All')
                ->badge($model::query()->count()),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $model::STATUS_PENDING))
                ->badge($model::query()->where('status', $model::STATUS_PENDING)->count()),
            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $model::STATUS_PAID))
                ->badge($model::query()->where('status', $model::STATUS_PAID)->count()),
            'failed' => Tab::make('Failed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $model::STATUS_FAILED))
                ->badge($model::query()->where('status', $model::STATUS_FAILED)->count()),
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $model::STATUS_CANCELLED))
                ->badge($model::query()->where('status', $model::STATUS_CANCELLED)->count()),
        ];
    }

}

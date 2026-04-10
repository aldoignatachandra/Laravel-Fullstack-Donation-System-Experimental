<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CampaignStatusStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getColumns(): int
    {
        return 3;
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $total = Campaign::query()->count();
        $draft = Campaign::query()->where('status', Campaign::STATUS_DRAFT)->count();
        $active = Campaign::query()->where('status', Campaign::STATUS_ACTIVE)->count();
        $paused = Campaign::query()->where('status', Campaign::STATUS_PAUSED)->count();
        $completed = Campaign::query()->where('status', Campaign::STATUS_COMPLETED)->count();
        $cancelled = Campaign::query()->where('status', Campaign::STATUS_CANCELLED)->count();

        return [
            Stat::make('Total Kampanye', (string) $total)
                ->description('Jumlah kampanye yang ada')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('gray')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Draft', (string) $draft)
                ->description('Kampanye dalam persiapan')
                ->descriptionIcon('heroicon-m-document')
                ->color('gray')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Aktif', (string) $active)
                ->description('Kampanye berjalan')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('success')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Paused', (string) $paused)
                ->description('Dihentikan sementara')
                ->descriptionIcon('heroicon-m-pause-circle')
                ->color('warning')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Completed', (string) $completed)
                ->description('Selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Cancelled', (string) $cancelled)
                ->description('Dibatalkan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),
        ];
    }
}

<?php

namespace App\Filament\Resources\Campaigns\Widgets;

use App\Models\Campaign;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CampaignsByStatusWidget extends StatsOverviewWidget
{
    protected static ?string $maxHeight = 'auto';

    protected function getColumns(): int
    {
        // Menampilkan semua stat dalam 1 baris
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Kampanye', Campaign::count())
                ->description('Jumlah kampanye yang ada')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Draft', Campaign::where('status', Campaign::STATUS_DRAFT)->count())
                ->description('Kampanye dalam tahap persiapan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Aktif', Campaign::where('status', Campaign::STATUS_ACTIVE)->count())
                ->description('Kampanye yang sedang berjalan')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('success')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Paused', Campaign::where('status', Campaign::STATUS_PAUSED)->count())
                ->description('Kampanye yang dihentikan sementara')
                ->descriptionIcon('heroicon-m-pause-circle')
                ->color('warning')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Completed', Campaign::where('status', Campaign::STATUS_COMPLETED)->count())
                ->description('Kampanye yang telah selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Cancelled', Campaign::where('status', Campaign::STATUS_CANCELLED)->count())
                ->description('Kampanye yang dibatalkan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),
        ];
    }
}

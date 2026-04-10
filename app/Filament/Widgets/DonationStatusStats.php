<?php

namespace App\Filament\Widgets;

use App\Helper\NumberHelper;
use App\Models\Donation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DonationStatusStats extends StatsOverviewWidget
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
        $total = Donation::query()->sum('amount');
        $pending = Donation::query()->where('status', Donation::STATUS_PENDING)->sum('amount');
        $paid = Donation::query()->where('status', Donation::STATUS_PAID)->sum('amount');
        $failed = Donation::query()->where('status', Donation::STATUS_FAILED)->sum('amount');
        $cancelled = Donation::query()->where('status', Donation::STATUS_CANCELLED)->sum('amount');

        return [
            Stat::make('Total Donasi',  NumberHelper::formatIDR($total))
                ->description('Total nilai donasi')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('gray')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Pending',  NumberHelper::formatIDR($pending))
                ->description('Menunggu pembayaran')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Paid',  NumberHelper::formatIDR($paid))
                ->description('Pembayaran berhasil')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Failed',  NumberHelper::formatIDR($failed))
                ->description('Pembayaran gagal')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),

            Stat::make('Cancelled',  NumberHelper::formatIDR($cancelled))
                ->description('Dibatalkan')
                ->descriptionIcon('heroicon-m-stop-circle')
                ->color('warning')
                ->extraAttributes(['class' => 'text-xs py-2 px-2']),
        ];
    }
}



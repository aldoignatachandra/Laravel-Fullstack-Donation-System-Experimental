<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DonationsCollectedBarChart extends ChartWidget
{
    use HasFiltersSchema;

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $now = Carbon::now();
        $defaultStart = $now->copy()->subMonths(11)->startOfMonth();
        $defaultEnd = $now->copy()->endOfMonth();

        $start = isset($this->filters['startDate']) && $this->filters['startDate']
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : $defaultStart;

        $end = isset($this->filters['endDate']) && $this->filters['endDate']
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : $defaultEnd;

        $months = collect();
        $cursor = $start->copy();
        while ($cursor->startOfMonth() <= $end->endOfMonth()) {
            $months->push($cursor->copy());
            $cursor->addMonth();
        }

        /** @var Collection<int, array{label:string,total:float}> $data */
        $data = $months->map(function (Carbon $month) {
            $label = $month->isoFormat('MMM YYYY');

            $total = Donation::query()
                ->where('status', Donation::STATUS_PAID)
                ->whereBetween('paid_at', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])
                ->sum('amount');

            return [
                'label' => $label,
                'total' => (float) $total,
            ];
        });

        return [
            'labels' => $data->pluck('label')->all(),
            'datasets' => [
                [
                    'label' => 'Total Donasi (IDR)',
                    'data' => $data->pluck('total')->all(),
                ],
            ],
        ];
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('startDate')
                ->label('Mulai')
                ->default(now()->subMonths(11)->startOfMonth()),
            DatePicker::make('endDate')
                ->label('Sampai')
                ->default(now()->endOfMonth()),
        ]);
    }
}

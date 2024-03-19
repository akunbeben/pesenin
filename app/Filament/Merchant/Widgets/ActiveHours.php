<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Order;
use Carbon\CarbonPeriod;
use Filament\Facades\Filament;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Pennant\Feature;

class ActiveHours extends ChartWidget
{
    public ?string $filter = 'hourly';

    public function getHeading(): string | Htmlable | null
    {
        return match ($this->filter) {
            'daily' => __('Peak days'),
            default => __('Peak hours'),
        };
    }

    protected function getFilters(): ?array
    {
        return [
            'hourly' => __('Hourly'),
            'daily' => __('Daily'),
        ];
    }

    public function getColumnSpan(): int | string | array
    {
        return ! Feature::for(Filament::getTenant())->active('feature_payment') ? [
            'default' => 6,
            'sm' => 3,
        ] : [
            'default' => 'full',
            'sm' => 6,
            'md' => 3,
            'lg' => 3,
            'xl' => 3,
            '2xl' => 3,
        ];
    }

    protected function getData(): array
    {
        $period = match ($this->filter) {
            'daily' => $this->peakDays(),
            default => $this->peakHours(),
        };

        return [
            'datasets' => [
                [
                    'label' => '',
                    'data' => $period->pluck('aggregate'),
                    'tension' => 0.5,
                    'fill' => true,
                ],
            ],
            'labels' => $period->pluck('date'),
        ];
    }

    protected function peakDays(): Collection
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $orders = Order::query()
            ->whereRelation('payment', fn (Builder $query) => $query->whereBelongsTo(
                Filament::getTenant(),
            ))
            ->selectRaw("
                date_format(created_at, '%W') AS date,
                COUNT(*) as aggregate
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return collect(CarbonPeriod::create($start, '1 Day', $end))
            ->map(fn ($time) => [
                'date' => $time->isoFormat('dddd'),
                'aggregate' => $orders->where('date', $time->format('l'))->value('aggregate', 0),
            ]);
    }

    protected function peakHours(): Collection
    {
        $start = Carbon::parse('00:00');
        $end = Carbon::parse('23:59');

        $orders = Order::query()
            ->whereRelation('payment', fn (Builder $query) => $query->whereBelongsTo(
                Filament::getTenant(),
            ))
            ->selectRaw("
                date_format(created_at, '%H:00') AS date,
                COUNT(*) as aggregate
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return collect(CarbonPeriod::create($start, '1 Hour', $end))
            ->map(fn ($time) => [
                'date' => $time->format('H:i'),
                'aggregate' => $orders->where('date', $time->format('H:i'))->value('aggregate', 0),
            ]);
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'line';
    }
}

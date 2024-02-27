<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Laravel\Pennant\Feature;

class ActiveHours extends ChartWidget
{
    public function getHeading(): string | Htmlable | null
    {
        return __('Active hours');
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
        $orders = Trend::query(Order::query()->whereRelation('payment', function (Builder $query) {
            $query->whereBelongsTo(Filament::getTenant());
        }))
            ->dateColumn('created_at')
            ->between(
                start: now()->startOfDay(),
                end: now()->endOfDay(),
            )
            ->perHour()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => '',
                    'data' => $orders->map(fn (TrendValue $value) => $value->aggregate),
                    'tension' => 0.5,
                    'fill' => true,
                ],
            ],
            'labels' => $orders->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('H:i')),
        ];
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

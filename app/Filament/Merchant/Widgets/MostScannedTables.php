<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Table;
use App\Traits\Orders\Status;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Pennant\Feature;

class MostScannedTables extends ChartWidget
{
    public function getHeading(): string | Htmlable | null
    {
        return __('Most orders from table');
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
        $tables = Table::query()
            ->withCount('orders')
            ->whereRelation('orders', 'status', Status::Success)
            ->whereHas('orders')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('Orders count'),
                    'data' => $tables->pluck('orders_count'),
                    'tension' => 0.5,
                    'fill' => true,
                    'backgroundColor' => '#0ea5e9',
                    'borderWidth' => 0,
                    'borderRadius' => 15,
                ],
            ],
            'labels' => $tables->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

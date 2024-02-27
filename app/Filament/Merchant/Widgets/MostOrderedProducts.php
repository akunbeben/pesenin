<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Pennant\Feature;

class MostOrderedProducts extends ChartWidget
{
    public function getHeading(): string | Htmlable | null
    {
        return __('Most ordered products');
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
        $products = Product::query()->withCount('orders')->whereHas('orders')->get();

        return [
            'datasets' => [
                [
                    'label' => __('Orders count'),
                    'data' => $products->pluck('orders_count'),
                    'tension' => 0.5,
                    'fill' => true,
                    'backgroundColor' => '#0ea5e9',
                    'borderWidth' => 0,
                    'borderRadius' => 15,
                ],
            ],
            'labels' => $products->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

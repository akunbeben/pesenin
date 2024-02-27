<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;

class MostUsedPayments extends ChartWidget
{
    public function getHeading(): string|Htmlable|null
    {
        return __('Most used payment methods');
    }

    public function getColumnSpan(): int | string | array
    {
        return !Feature::for(Filament::getTenant())->active('feature_payment') ? [
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
        $payments = Payment::query()
            ->select([
                'data->payment_channel as payment_channel',
                DB::raw('COUNT(`payments`.`id`) as counted'),
            ])
            ->whereBelongsTo(Filament::getTenant())
            ->groupBy('payment_channel')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('Orders count'),
                    'data' => $payments->pluck('counted'),
                    'tension' => 0.5,
                    'fill' => true,
                    'backgroundColor' => '#0ea5e9',
                    'borderWidth' => 0,
                    'borderRadius' => 15,
                ],
            ],
            'labels' => $payments->pluck('payment_channel'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

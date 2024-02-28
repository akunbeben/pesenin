<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SalesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $salesToday = Order::query()->paid()->today()->sum('total');
        $salesThisMonth = Order::query()->paid()->thisMonth()->sum('total');

        return [
            Stat::make(__('Sales today'), Number::currency($salesToday, 'IDR', config('app.locale'))),
            Stat::make(__('Sales last month'), Number::currency($salesThisMonth, 'IDR', config('app.locale'))),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}

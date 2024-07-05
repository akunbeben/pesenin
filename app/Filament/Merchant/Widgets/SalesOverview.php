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
        $salesCashToday = Order::query()->paid()->today()->cash()->sum('total');
        $salesCashThisMonth = Order::query()->paid()->thisMonth()->cash()->sum('total');

        return [
            Stat::make(__('Sales today'), Number::currency($salesToday, 'IDR', config('app.locale')))
                ->description(__('Aggregated data between Payment gateway and Cash')),
            Stat::make(__('Sales last month'), Number::currency($salesThisMonth, 'IDR', config('app.locale')))
                ->description(__('Aggregated data between Payment gateway and Cash')),
            Stat::make(__('Cash sales today'), Number::currency($salesCashToday, 'IDR', config('app.locale'))),
            Stat::make(__('Cash sales last month'), Number::currency($salesCashThisMonth, 'IDR', config('app.locale'))),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}

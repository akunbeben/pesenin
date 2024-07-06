<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SalesDetailOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $salesCashToday = Order::query()->paid()->today()->cash()->sum('total');
        $salesCashThisMonth = Order::query()->paid()->thisMonth()->cash()->sum('total');
        $salesNonCashToday = Order::query()->paid()->today()->nonCash()->sum('total');
        $salesNonCashThisMonth = Order::query()->paid()->thisMonth()->nonCash()->sum('total');

        return [
            Stat::make(__('Cash sales today'), Number::currency($salesCashToday, 'IDR', config('app.locale'))),
            Stat::make(__('Cash sales last month'), Number::currency($salesCashThisMonth, 'IDR', config('app.locale'))),
            Stat::make(__('Sales today (payment gateway)'), Number::currency($salesNonCashToday, 'IDR', config('app.locale'))),
            Stat::make(__('Sales last month (payment gateway)'), Number::currency($salesNonCashThisMonth, 'IDR', config('app.locale'))),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}

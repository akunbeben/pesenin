<?php

namespace App\Filament\Merchant\Widgets;

use App\Jobs\BunRunner;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class BunRunnerWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        if (! app()->isProduction()) {
            if (! ($buildTime = Cache::get('bun-build-time'))) {
                BunRunner::dispatch();
            }
        }

        return [
            Stat::make(__('Bun elapsed time'), $buildTime ?? 'Processing')->icon('heroicon-m-clock'),
        ];
    }
}

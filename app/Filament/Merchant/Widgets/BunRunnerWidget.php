<?php

namespace App\Filament\Merchant\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;

class BunRunnerWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $buildTime = Cache::remember('bun-build-time', now()->addHour(), function () {
            $start = microtime(true);
            $process = Process::path('/')->start('/home/akunbeben/.bun/bin/bun --cwd /home/akunbeben/Projects/pesenin/ build');

            while ($process->running()) {
                // ...
            }

            $process->wait();

            return number_format((microtime(true) - $start), '2') . 's';
        });

        return [
            // Stat::make(__('Bun elapsed time (Dev only)'), $buildTime)->icon('heroicon-m-clock')
        ];
    }
}

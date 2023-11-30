<?php

namespace App\Filament\Merchant\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Xendit\BalanceAndTransaction\BalanceApi;

class MerchantOverview extends BaseWidget
{
    public $balance = 0;

    public $holding = 0;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function mount(): void
    {
        \Xendit\Configuration::setXenditKey(config('services.xendit.secret_key'));

        $this->balance = (new BalanceApi())->getBalance(for_user_id: Filament::getTenant()->business_id)['balance'];
        $this->holding = (new BalanceApi())->getBalance('HOLDING', for_user_id: Filament::getTenant()->business_id)['balance'];
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Balance'), Number::currency($this->balance, 'IDR', 'id'))
                ->icon('heroicon-m-banknotes')
                ->description(__('Data will refresh every hour')),
            Stat::make(__('On-Hold'), Number::currency($this->holding, 'IDR', 'id'))
                ->icon('heroicon-m-hand-raised')
                ->description(__('Data will refresh every hour')),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}

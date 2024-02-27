<?php

namespace App\Filament\Merchant\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;
use Laravel\Pennant\Feature;
use Xendit\BalanceAndTransaction\BalanceApi;

class MerchantOverview extends BaseWidget
{
    public $balance = 0;

    public $holding = 0;

    protected static ?int $sort = 1;

    public function getColumnSpan(): int | string | array
    {
        return ! Feature::for(Filament::getTenant())->active('feature_payment') ? [
            'default' => 6,
            'sm' => 6,
        ] : [
            'default' => 'full',
            'sm' => 6,
            'md' => 6,
            'lg' => 6,
            'xl' => 6,
            '2xl' => 6,
        ];
    }

    public function mount(): void
    {
        \Xendit\Configuration::setXenditKey(config('services.xendit.secret_key'));

        /** @var \App\Models\Merchant $merchant */
        $merchant = Filament::getTenant();

        $this->balance = match ($merchant->business_id) {
            null => 0,
            default => Cache::remember("merchant_{$merchant->business_id}_balance", now()->addMinutes(10), function () use ($merchant) {
                return (new BalanceApi())->getBalance(for_user_id: $merchant->business_id)['balance'];
            }),
        };

        $this->holding = match ($merchant->business_id) {
            null => 0,
            default => Cache::remember("merchant_{$merchant->business_id}_holding", now()->addMinutes(10), function () use ($merchant) {
                return (new BalanceApi())->getBalance('HOLDING', for_user_id: $merchant->business_id)['balance'];
            }),
        };
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Balance (Payment gateway)'), Number::currency($this->balance, 'IDR', 'id'))
                ->icon('heroicon-m-banknotes')
                ->description(__('Data will refresh every 10 minutes')),
            Stat::make(__('On-Hold (Payment gateway)'), Number::currency($this->holding, 'IDR', 'id'))
                ->icon('heroicon-m-hand-raised')
                ->description(__('Data will refresh every 10 minutes')),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}

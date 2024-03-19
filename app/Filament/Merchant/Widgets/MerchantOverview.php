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

    public $tax = 0;

    public bool $xenditNotReady = false;

    public bool $fetchFailed = false;

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

        try {
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

            $this->tax = match ($merchant->business_id) {
                null => 0,
                default => Cache::remember("merchant_{$merchant->business_id}_tax", now()->addMinutes(10), function () use ($merchant) {
                    return (new BalanceApi())->getBalance('TAX', for_user_id: $merchant->business_id)['balance'];
                }),
            };
        } catch (\Xendit\XenditSdkException $th) {
            $this->xenditNotReady = true;

            logger()->error('Xendit error: ', $th->getTrace());
        } catch (\Throwable $th) {
            $this->fetchFailed = true;

            logger()->error('Xendit error: ', $th->getTrace());
        }
    }

    protected function getStats(): array
    {
        if ($this->xenditNotReady) {
            return [
                Stat::make(__('Failed to fetch data.'), null)
                    ->icon('heroicon-m-document-check')
                    ->description(__('Please activate your xendit account.')),
            ];
        }

        if ($this->fetchFailed) {
            return [
                Stat::make(__('Failed to fetch data.'), null)
                    ->icon('heroicon-m-document-check')
                    ->description(__('Unexpected error, try again.')),
            ];
        }

        return [
            Stat::make(__('Balance (Payment gateway)'), Number::currency($this->balance, 'IDR', 'id'))
                ->icon('heroicon-m-banknotes')
                ->description(__('Data will refresh every 10 minutes'))
                ->extraAttributes(['class' => 'overview', 'title' => Number::currency($this->balance, 'IDR', 'id')]),
            Stat::make(__('On-Hold (Payment gateway)'), Number::currency($this->holding, 'IDR', 'id'))
                ->icon('heroicon-m-hand-raised')
                ->description(__('Data will refresh every 10 minutes'))
                ->extraAttributes(['class' => 'overview', 'title' => Number::currency($this->holding, 'IDR', 'id')]),
            Stat::make(__('Tax (Payment gateway)'), Number::currency($this->tax, 'IDR', 'id'))
                ->icon('heroicon-m-chevron-up-down')
                ->description(__('Data will refresh every 10 minutes'))
                ->extraAttributes(['class' => 'overview', 'title' => Number::currency($this->tax, 'IDR', 'id')]),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}

<?php

namespace App\Filament\Merchant\Pages;

use App\Filament\Merchant\Widgets\LatestTransactions;
use App\Filament\Merchant\Widgets\MerchantOverview;
use App\Filament\Merchant\Widgets\QRCode;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Pennant\Feature;

class BaseDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-m-home';

    public static function getNavigationLabel(): string
    {
        return __('Dashboard');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Dashboard');
    }

    public function getColumns(): int | string | array
    {
        return 6;
    }

    public function getWidgets(): array
    {
        $widgets = [
            MerchantOverview::class,
            LatestTransactions::class,
        ];

        Feature::for(Filament::getTenant())->all();

        if (Feature::for(Filament::getTenant())->active('ikiosk')) {
            $widgets[] = QRCode::class;
        }

        return $widgets;
    }
}

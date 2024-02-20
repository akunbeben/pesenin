<?php

namespace App\Filament\Merchant\Pages;

use App\Filament\Merchant\Widgets\BunRunnerWidget;
use App\Filament\Merchant\Widgets\LatestTransactions;
use App\Filament\Merchant\Widgets\MerchantOverview;
use App\Filament\Merchant\Widgets\QRCode;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Pennant\Feature;

class BaseDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

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
        return Filament::getTenant()->business_id ? 6 : 2;
    }

    public function getWidgets(): array
    {
        /** @var \App\Models\Merchant $merchant */
        $merchant = Filament::getTenant();

        if (! $merchant->business_id) {
            return [
                AccountWidget::class,
                FilamentInfoWidget::class,
            ];
        }

        $widgets = [
            (new class extends AccountWidget
            {
                protected int | string | array $columnSpan = 3;

                public static function make(array $properties = []): WidgetConfiguration
                {
                    return app(WidgetConfiguration::class, ['widget' => self::class, 'properties' => $properties]);
                }
            })->make(),
            (new class extends FilamentInfoWidget
            {
                protected int | string | array $columnSpan = 3;

                public static function make(array $properties = []): WidgetConfiguration
                {
                    return app(WidgetConfiguration::class, ['widget' => self::class, 'properties' => $properties]);
                }
            })->make(),
            MerchantOverview::class,
            LatestTransactions::class,
        ];

        if (Feature::for($merchant)->active('feature_ikiosk')) {
            $widgets[] = QRCode::class;
        }

        if (! app()->isProduction()) {
            $widgets[] = BunRunnerWidget::class;
        }

        return $widgets;
    }
}

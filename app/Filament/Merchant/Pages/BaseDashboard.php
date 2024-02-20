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
        return 6;
    }

    public function getWidgets(): array
    {
        /** @var \App\Models\Merchant $merchant */
        $merchant = Filament::getTenant();

        Feature::for($merchant)->all();

        if (! Feature::for($merchant)->active('feature_payment')) {
            return [
                (new class extends AccountWidget
                {
                    public function getColumnSpan(): int | string | array
                    {
                        return ! Feature::for(Filament::getTenant())->active('feature_payment') ? [
                            'default' => 6,
                            'sm' => 3,
                        ] : [
                            'default' => 'full',
                            'sm' => 6,
                            'md' => 4,
                            'lg' => 4,
                            'xl' => 4,
                            '2xl' => 4,
                        ];
                    }

                    public static function make(array $properties = []): WidgetConfiguration
                    {
                        return app(WidgetConfiguration::class, ['widget' => self::class, 'properties' => $properties]);
                    }
                })->make(),
                (new class extends FilamentInfoWidget
                {
                    public function getColumnSpan(): int | string | array
                    {
                        return ! Feature::for(Filament::getTenant())->active('feature_payment') ? [
                            'default' => 6,
                            'sm' => 3,
                        ] : [
                            'default' => 'full',
                            'sm' => 6,
                            'md' => 4,
                            'lg' => 4,
                            'xl' => 4,
                            '2xl' => 4,
                        ];
                    }

                    public static function make(array $properties = []): WidgetConfiguration
                    {
                        return app(WidgetConfiguration::class, ['widget' => self::class, 'properties' => $properties]);
                    }
                })->make(),
                QRCode::class,
            ];
        }

        $widgets = [
            (new class extends AccountWidget
            {
                public function getColumnSpan(): int | string | array
                {
                    return ! Feature::for(Filament::getTenant())->active('feature_payment') ? [
                        'default' => 6,
                        'sm' => 3,
                    ] : [
                        'default' => 'full',
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 4,
                        'xl' => 4,
                        '2xl' => 4,
                    ];
                }

                public static function make(array $properties = []): WidgetConfiguration
                {
                    return app(WidgetConfiguration::class, ['widget' => self::class, 'properties' => $properties]);
                }
            })->make(),
            (new class extends FilamentInfoWidget
            {
                public function getColumnSpan(): int | string | array
                {
                    return ! Feature::for(Filament::getTenant())->active('feature_payment') ? [
                        'default' => 6,
                        'sm' => 3,
                    ] : [
                        'default' => 'full',
                        'sm' => 6,
                        'md' => 4,
                        'lg' => 4,
                        'xl' => 4,
                        '2xl' => 4,
                    ];
                }

                public static function make(array $properties = []): WidgetConfiguration
                {
                    return app(WidgetConfiguration::class, ['widget' => self::class, 'properties' => $properties]);
                }
            })->make(),
            MerchantOverview::class,
            LatestTransactions::class,
        ];

        if (! app()->isProduction()) {
            $widgets[] = BunRunnerWidget::class;
        }

        return $widgets;
    }
}

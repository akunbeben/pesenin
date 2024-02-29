<?php

namespace App\Filament\Merchant\Pages;

use App\Events\PaymentFeatureActivated;
use App\Filament\Merchant\Widgets\ActiveHours;
use App\Filament\Merchant\Widgets\LatestTransactions;
use App\Filament\Merchant\Widgets\MerchantOverview;
use App\Filament\Merchant\Widgets\MostOrderedProducts;
use App\Filament\Merchant\Widgets\MostScannedTables;
use App\Filament\Merchant\Widgets\MostUsedPayments;
use App\Filament\Merchant\Widgets\QRCode;
use App\Filament\Merchant\Widgets\SalesOverview;
use App\Filament\Merchant\Widgets\XenditProgress;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Support\Enums\ActionSize;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Laravel\Pennant\Feature;

class BaseDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderActions(): array
    {
        /** @var \App\Models\Merchant $merchant */
        $merchant = Filament::getTenant();

        return [
            Action::make('enable_payment')
                ->hidden(
                    $merchant->xendit_in_progress
                        || ! Feature::active('can-have-payment')
                        || Feature::for($merchant)->active('feature_payment')
                )
                ->action(function () use ($merchant) {
                    $merchant->update(['xendit_in_progress' => true]);

                    PaymentFeatureActivated::dispatch($merchant);
                })
                ->requiresConfirmation()
                ->modalDescription(__('Activating this feature will allow merchant to have payment capability.'))
                ->color('gray')
                ->icon('heroicon-o-credit-card')
                ->size(ActionSize::Large)
                ->translateLabel(),
        ];
    }

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

        if ($merchant->xendit_in_progress) {
            return [XenditProgress::class];
        }

        if (! Filament::getTenant()->loadMissing('setting')->setting->payment) {
            return [
                (new class extends AccountWidget
                {
                    public function getColumnSpan(): int | string | array
                    {
                        return ! Filament::getTenant()->loadMissing('setting')->setting->payment ? [
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

                    public static function make(array $properties = []): WidgetConfiguration
                    {
                        return app(WidgetConfiguration::class, ['widget' => self::class, 'properties' => $properties]);
                    }
                })->make(),
                QRCode::class,
            ];
        }

        $widgets = [
            MerchantOverview::class,
            SalesOverview::class,
            ActiveHours::class,
            MostScannedTables::class,
            MostUsedPayments::class,
            MostOrderedProducts::class,
            LatestTransactions::class,
        ];

        return $widgets;
    }
}

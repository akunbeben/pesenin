<?php

namespace App\Providers\Filament;

use App\Filament\Merchant\Pages\BaseDashboard;
use App\Filament\Merchant\Pages\MerchantProfile;
use App\Filament\Merchant\Pages\MerchantRegistration;
use App\Filament\Merchant\Pages\ResetPassword;
use App\Models\Merchant;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MerchantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('merchant')
            ->path('merchant')
            ->default()
            ->login()
            ->passwordReset(resetAction: ResetPassword::class)
            ->registration(null)
            ->tenant(Merchant::class, 'uuid')
            ->tenantRegistration(MerchantRegistration::class)
            ->tenantProfile(MerchantProfile::class)
            ->favicon(asset('apple-touch-icon.png'))
            ->colors([
                'danger' => Color::Rose,
                'info' => Color::Blue,
                'primary' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->viteTheme('resources/css/filament/merchant/theme.css')
            ->font('Be Vietnam Pro', 'https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700')
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(fn () => view('components.app-logo'))
            ->brandLogoHeight('2.5rem')
            ->discoverResources(in: app_path('Filament/Merchant/Resources'), for: 'App\\Filament\\Merchant\\Resources')
            ->discoverPages(in: app_path('Filament/Merchant/Pages'), for: 'App\\Filament\\Merchant\\Pages')
            ->databaseNotifications()
            ->pages([
                BaseDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Merchant/Widgets'), for: 'App\\Filament\\Merchant\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

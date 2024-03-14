<?php

namespace App\Providers\Filament;

use App\Filament\Merchant\Pages\ResetPassword;
use App\Filament\Merchant\Pages\UserProfile;
use App\Filament\Outlet\Pages\Login;
use App\Http\Middleware\EnsureEmployee;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class OutletPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('outlet')
            ->path('outlet')
            ->spa()
            ->topNavigation()
            ->login(Login::class)
            ->passwordReset(resetAction: ResetPassword::class)
            ->registration(null)
            ->profile(UserProfile::class)
            ->colors([
                'danger' => Color::Rose,
                'info' => Color::Blue,
                'primary' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->favicon(asset('apple-touch-icon.png'))
            ->viteTheme('resources/css/filament/outlet/theme.css')
            ->font('Be Vietnam Pro', 'https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700')
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(fn () => view('components.app-logo'))
            ->brandLogoHeight('2.5rem')
            ->discoverResources(in: app_path('Filament/Outlet/Resources'), for: 'App\\Filament\\Outlet\\Resources')
            ->discoverPages(in: app_path('Filament/Outlet/Pages'), for: 'App\\Filament\\Outlet\\Pages')
            ->pages([
                \App\Filament\Outlet\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Outlet/Widgets'), for: 'App\\Filament\\Outlet\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
                EnsureEmployee::class,
                Authenticate::class,
            ]);
    }
}

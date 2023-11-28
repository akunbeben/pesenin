<?php

namespace App\Providers;

use App\Models\Merchant;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme(config('app.scheme'));

        Feature::define('ikiosk', fn (Merchant $merchant) => $merchant->setting->ikiosk_mode);
    }
}

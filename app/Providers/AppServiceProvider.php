<?php

namespace App\Providers;

use App\Models\Merchant;
use App\Support\DevelopmentUrlGenerator;
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
        if (! app()->isProduction()) {
            config(['media-library.url_generator' => DevelopmentUrlGenerator::class]);
        }

        $this->callAfterResolving('blade.compiler', function ($blade) {
            $blade->if('features', function ($feature, $merchant, $value = null) {
                if (func_num_args() === 3) {
                    return Feature::for($merchant)->value($feature) === $value;
                }

                return Feature::for($merchant)->active($feature);
            });
        });

        Feature::define('ikiosk', fn (Merchant $merchant) => $merchant->setting->ikiosk_mode);
        Feature::define('tax', fn (Merchant $merchant) => $merchant->setting->tax);
        Feature::define('fee', fn (Merchant $merchant) => $merchant->setting->fee);

        URL::forceScheme(config('app.scheme'));
        setlocale(LC_TIME, 'id_ID');
        \Carbon\Carbon::setLocale('id');
    }
}

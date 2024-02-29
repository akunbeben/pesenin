<?php

namespace App\Providers;

use App\Models\Merchant;
use App\Models\User;
use App\Support\DevelopmentUrlGenerator;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
        Model::preventSilentlyDiscardingAttributes(false);

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

        Feature::define('can-have-payment', fn (User $user) => $user->paid);
        Feature::define('feature_ikiosk', fn (Merchant $merchant) => $merchant->loadMissing('setting')->setting->ikiosk_mode);
        Feature::define('feature_tax', fn (Merchant $merchant) => $merchant->loadMissing('setting')->setting->tax);
        Feature::define('feature_fee', fn (Merchant $merchant) => $merchant->loadMissing('setting')->setting->fee);
        Feature::define('feature_payment', fn (Merchant $merchant) => (bool) $merchant->loadMissing('setting')->setting->payment);

        URL::forceRootUrl(config('app.asset_url'));
        URL::forceScheme(config('app.scheme'));
        setlocale(LC_TIME, 'id_ID');
        Carbon::setLocale(config('app.locale'));
        CarbonPeriod::setLocale(config('app.locale'));
        DB::statement("SET lc_time_names = 'id_ID'");
    }
}

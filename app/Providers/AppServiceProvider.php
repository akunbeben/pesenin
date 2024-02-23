<?php

namespace App\Providers;

use App\Models\Merchant;
use App\Models\User;
use App\Support\DevelopmentUrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
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
        Model::shouldBeStrict();
        Model::preventSilentlyDiscardingAttributes(false);

        if (!app()->isProduction()) {
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

        Gate::define('viewPulse', function (User $user) {
            return in_array($user->email, ['akunbeben@gmail.com', 'beben.devs@gmail.com']);
        });

        Feature::define('system_ikiosk', fn (Merchant $merchant) => false);
        Feature::define('feature_ikiosk', fn (Merchant $merchant) => $merchant->loadMissing('setting')->setting->ikiosk_mode);
        Feature::define('feature_tax', fn (Merchant $merchant) => $merchant->loadMissing('setting')->setting->tax);
        Feature::define('feature_fee', fn (Merchant $merchant) => $merchant->loadMissing('setting')->setting->fee);
        Feature::define('feature_payment', fn (Merchant $merchant) => $merchant->loadMissing(['user'])->user->paid && (bool) $merchant->business_id);

        URL::forceScheme(config('app.scheme'));
        setlocale(LC_TIME, 'id_ID');
        \Carbon\Carbon::setLocale('id');
    }
}

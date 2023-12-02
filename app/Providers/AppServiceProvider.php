<?php

namespace App\Providers;

use App\Models\Merchant;
use App\Models\User;
use App\Support\DevelopmentUrlGenerator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
use Laravel\Pulse\Facades\Pulse;

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

        Gate::define('viewPulse', function (User $user) {
            return in_array($user->email, ['akunbeben@gmail.com', 'beben.devs@gmail.com']);
        });

        Feature::define('ikiosk', fn (Merchant $merchant) => $merchant->setting->ikiosk_mode);
        Feature::define('tax', fn (Merchant $merchant) => $merchant->setting->tax);
        Feature::define('fee', fn (Merchant $merchant) => $merchant->setting->fee);

        URL::forceScheme(config('app.scheme'));
        setlocale(LC_TIME, 'id_ID');
        \Carbon\Carbon::setLocale('id');

        Pulse::users(function ($ids) {
            return User::findMany($ids)->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'extra' => $user->email,
                'email' => $user->email,
                'avatar' => $user->getFilamentAvatarUrl(),
            ]);
        });
    }
}

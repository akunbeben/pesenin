<?php

namespace App\Listeners;

use App\Events\CloudflareForwarded;
use App\Events\MerchantRegistrationCompleted;
use App\Events\SkippedBusinessRegistration;
use App\Services\Cloudflare\Routing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\URL;
use Laravel\Pennant\Feature;

class ForwardingCloudflare implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(private Routing $routing)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MerchantRegistrationCompleted $event): void
    {
        if (! app()->isProduction()) {
            URL::forceRootUrl(config('app.asset_url'));
        }

        if (! $event->merchant->xendit_in_progress) {
            $event->merchant->update(['xendit_in_progress' => true]);
        }

        $event->merchant->update([
            'cloudflare_email' => $this->routing->forward(
                destination: $event->user->email,
                suffix: $event->merchant->name
            ),
        ]);

        if (Feature::for($event->user)->active('can-have-payment')) {
            match ($event->withPayment) {
                true => CloudflareForwarded::dispatch($event->user, $event->merchant, $event->withPayment),
                false => SkippedBusinessRegistration::dispatch($event->merchant, null, null),
            };
        }
    }
}

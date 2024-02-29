<?php

namespace App\Listeners;

use App\Events\CloudflareForwarded;
use App\Events\PaymentFeatureActivated;

class ActivatingPaymentFeature
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentFeatureActivated $event): void
    {
        CloudflareForwarded::dispatch($event->merchant->user, $event->merchant, true);
    }
}

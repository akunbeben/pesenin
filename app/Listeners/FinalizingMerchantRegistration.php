<?php

namespace App\Listeners;

use App\Events\BusinessAccountRegistered;
use App\Events\SkippedBusinessRegistration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FinalizingMerchantRegistration
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
    public function handle(BusinessAccountRegistered|SkippedBusinessRegistration $event): void
    {
        $event->merchant->update([
            'business_id' => $event->businessId,
            'webhook_token' => $event->token,
            'xendit_in_progress' => false,
        ]);
    }
}

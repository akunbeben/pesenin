<?php

namespace App\Listeners;

use App\Events\BusinessAccountRegistered;
use App\Events\SkippedBusinessRegistration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class FinalizingMerchantRegistration implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(BusinessAccountRegistered | SkippedBusinessRegistration $event): void
    {
        $event->merchant->update([
            'business_id' => $event->businessId,
            'webhook_token' => $event->token,
            'xendit_in_progress' => false,
        ]);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            BusinessAccountRegistered::class,
            [FinalizingMerchantRegistration::class, 'handle']
        );

        $events->listen(
            SkippedBusinessRegistration::class,
            [FinalizingMerchantRegistration::class, 'handle']
        );
    }
}

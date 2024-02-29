<?php

namespace App\Listeners;

use App\Events\BusinessAccountRegistered;
use App\Events\CloudflareForwarded;
use App\Services\Xendit\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Pennant\Feature;

class RegisteringBusinessAccount implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(private Account $account)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CloudflareForwarded $event): void
    {
        $businessId = $this->account->createAccount(
            data: [
                'email' => $event->merchant->cloudflare_email,
                'type' => 'MANAGED',
                'public_profile' => [
                    'business_name' => $event->merchant->name,
                ],
            ]
        );

        $token = $this->account->registerWebhook(
            type: 'invoice',
            url: route('webhooks.payment.success', [$event->merchant]),
            businessId: $businessId
        );

        Feature::for($event->merchant)->activate('feature_payment');
        $event->merchant->setting->update(['payment' => $event->withPayment]);

        BusinessAccountRegistered::dispatch($event->merchant, $businessId, $token);
    }
}

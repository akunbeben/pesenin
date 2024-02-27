<?php

namespace App\Jobs;

use App\Models\Merchant;
use App\Models\User;
use App\Services\Cloudflare\Routing;
use App\Services\Xendit\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Laravel\Pennant\Feature;

class ForwardingEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public Merchant $merchant, public bool $withPayment)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(Routing $routing, Account $account): void
    {
        if (! app()->isProduction()) {
            URL::forceRootUrl(config('app.asset_url'));
        }

        $this->merchant->update(['xendit_in_progress' => true]);

        if (
            ($this->merchant->business_id && $this->merchant->webhook_token)
            || ! Feature::for($this->user)->active('can-have-payment')
        ) {
            Feature::for($this->merchant)->activate('feature_payment');
            $this->merchant->update(['xendit_in_progress' => false]);
            $this->merchant->setting->update(['payment' => true]);

            return;
        }

        $cloudflareEmail = $routing->forward(
            destination: $this->user->email,
            suffix: $this->merchant->name
        );

        if ($this->withPayment && Feature::for($this->user)->active('can-have-payment')) {
            $businessId = $account->createAccount(
                data: [
                    'email' => $cloudflareEmail,
                    'type' => 'MANAGED',
                    'public_profile' => [
                        'business_name' => $this->merchant->name,
                    ],
                ]
            );

            $token = $account->registerWebhook(
                type: 'invoice',
                url: route('webhooks.payment.success', [$this->merchant]),
                businessId: $businessId
            );

            Feature::for($this->merchant)->activate('feature_payment');
            $this->merchant->setting->update(['payment' => true]);
        }

        $this->merchant->update([
            'business_id' => $businessId ?? null,
            'cloudflare_email' => $cloudflareEmail,
            'webhook_token' => $token ?? null,
            'xendit_in_progress' => false,
        ]);
    }
}

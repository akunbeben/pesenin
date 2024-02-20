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

        $cloudflareEmail = $routing->forward($this->user->email, $this->merchant->name);

        if ($this->withPayment) {
            $businessId = $account->createAccount([
                'email' => $cloudflareEmail,
                'type' => 'MANAGED',
                'public_profile' => [
                    'business_name' => $this->merchant->name,
                ],
            ]);

            $token = $account->registerWebhook('invoice', route('webhooks.payment.success', [$this->merchant]), $businessId);
        }

        $this->merchant->update([
            'business_id' => $businessId ?? null,
            'cloudflare_email' => $cloudflareEmail,
            'webhook_token' => $token ?? null,
        ]);
    }
}

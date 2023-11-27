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

class ForwardingEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public Merchant $merchant)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(Routing $routing, Account $account): void
    {
        $cloudflareEmail = $routing->forward($this->user->email, $this->merchant->name);

        $businessId = $account->createAccount([
            'email' => $cloudflareEmail,
            'type' => 'MANAGED',
            'public_profile' => [
                'business_name' => $this->merchant->name,
            ],
        ]);

        $this->merchant->update([
            'business_id' => $businessId,
            'cloudflare_email' => $cloudflareEmail,
        ]);
    }
}

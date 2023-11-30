<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class ResetPassword extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected function resetUrl($notifiable): string
    {
        if (! app()->isProduction()) {
            URL::forceRootUrl(config('app.asset_url'));
        }

        return URL::signedRoute('filament.merchant.auth.password-reset.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}

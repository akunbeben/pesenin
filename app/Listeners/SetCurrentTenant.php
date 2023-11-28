<?php

namespace App\Listeners;

use Filament\Events\TenantSet;

class SetCurrentTenant
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
    public function handle(TenantSet $event): void
    {
        $event->getUser()->update(['active_merchant' => $event->getTenant()->getKey()]);
    }
}

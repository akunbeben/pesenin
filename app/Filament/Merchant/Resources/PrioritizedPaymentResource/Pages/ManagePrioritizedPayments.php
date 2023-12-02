<?php

namespace App\Filament\Merchant\Resources\PrioritizedPaymentResource\Pages;

use App\Filament\Merchant\Resources\PrioritizedPaymentResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManagePrioritizedPayments extends ManageRecords
{
    protected static string $resource = PrioritizedPaymentResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('Prioritized payment');
    }

    public function getTitle(): string
    {
        return __('Prioritized payment');
    }
}

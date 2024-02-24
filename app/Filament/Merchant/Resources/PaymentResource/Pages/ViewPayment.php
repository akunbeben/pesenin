<?php

namespace App\Filament\Merchant\Resources\PaymentResource\Pages;

use App\Filament\Merchant\Resources\PaymentResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    public function getHeading(): string | Htmlable
    {
        return $this->record->order->number;
    }

    public function getTitle(): string
    {
        return $this->record->order->number;
    }
}

<?php

namespace App\Filament\Outlet\Resources\PaymentResource\Pages;

use App\Filament\Outlet\Resources\PaymentResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManagePayments extends ManageRecords
{
    protected static string $resource = PaymentResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('Payment history');
    }

    public function getTitle(): string
    {
        return __('Payment history');
    }
}

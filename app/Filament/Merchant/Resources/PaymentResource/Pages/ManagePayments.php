<?php

namespace App\Filament\Merchant\Resources\PaymentResource\Pages;

use App\Filament\Merchant\Resources\PaymentResource;
use Filament\Actions;
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

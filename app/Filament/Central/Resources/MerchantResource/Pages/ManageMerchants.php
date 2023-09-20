<?php

namespace App\Filament\Central\Resources\MerchantResource\Pages;

use App\Filament\Central\Resources\MerchantResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMerchants extends ManageRecords
{
    protected static string $resource = MerchantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl')
                ->createAnother(false),
        ];
    }
}

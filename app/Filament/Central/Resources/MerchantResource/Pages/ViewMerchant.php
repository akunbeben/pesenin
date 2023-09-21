<?php

namespace App\Filament\Central\Resources\MerchantResource\Pages;

use App\Filament\Central\Resources\MerchantResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMerchant extends ViewRecord
{
    protected static string $resource = MerchantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-m-pencil-square')
                ->modalWidth('2xl'),
            Actions\DeleteAction::make()
                ->icon('heroicon-m-trash'),
        ];
    }
}

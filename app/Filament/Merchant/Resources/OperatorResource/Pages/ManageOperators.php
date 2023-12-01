<?php

namespace App\Filament\Merchant\Resources\OperatorResource\Pages;

use App\Filament\Merchant\Resources\OperatorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageOperators extends ManageRecords
{
    protected static string $resource = OperatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

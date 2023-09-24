<?php

namespace App\Filament\Merchant\Resources\TableResource\Pages;

use App\Filament\Merchant\Resources\TableResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTables extends ManageRecords
{
    protected static string $resource = TableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl')
                ->icon('heroicon-m-plus'),
        ];
    }
}

<?php

namespace App\Filament\Merchant\Resources\CategoryResource\Pages;

use App\Filament\Merchant\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->icon('heroicon-m-plus')->modalWidth('lg'),
        ];
    }
}

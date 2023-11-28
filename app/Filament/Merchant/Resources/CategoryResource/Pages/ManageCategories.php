<?php

namespace App\Filament\Merchant\Resources\CategoryResource\Pages;

use App\Filament\Merchant\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('Categories');
    }

    public function getTitle(): string
    {
        return __('Categories');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalHeading(__('Create :label', ['label' => __('category')]))
                ->icon('heroicon-m-plus')
                ->modalWidth('lg'),
        ];
    }
}

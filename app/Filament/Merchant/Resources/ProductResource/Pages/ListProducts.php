<?php

namespace App\Filament\Merchant\Resources\ProductResource\Pages;

use App\Filament\Merchant\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('List of :label', ['label' => __('product')]);
    }

    public function getTitle(): string
    {
        return __('List of :label', ['label' => __('product')]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->modalWidth('xl'),
        ];
    }
}

<?php

namespace App\Filament\Merchant\Resources\ProductResource\Pages;

use App\Filament\Merchant\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('Details of :label', ['label' => __('product')]);
    }

    public function getTitle(): string
    {
        return __('Details of :label', ['label' => __('product')]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

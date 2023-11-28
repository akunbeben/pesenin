<?php

namespace App\Filament\Merchant\Resources\ProductResource\Pages;

use App\Filament\Merchant\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('Edit :label', ['label' => __('product')]);
    }

    public function getTitle(): string
    {
        return __('Edit :label', ['label' => __('product')]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

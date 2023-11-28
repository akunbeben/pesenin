<?php

namespace App\Filament\Merchant\Resources\ProductResource\Pages;

use App\Filament\Merchant\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('Create :label', ['label' => __('product')]);
    }

    public function getTitle(): string
    {
        return __('Create :label', ['label' => __('product')]);
    }
}

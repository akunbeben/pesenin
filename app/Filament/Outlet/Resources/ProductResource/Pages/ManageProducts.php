<?php

namespace App\Filament\Outlet\Resources\ProductResource\Pages;

use App\Filament\Outlet\Resources\ProductResource;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('');
    }
}

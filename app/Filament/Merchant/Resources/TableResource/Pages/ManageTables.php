<?php

namespace App\Filament\Merchant\Resources\TableResource\Pages;

use App\Filament\Merchant\Resources\TableResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageTables extends ManageRecords
{
    protected static string $resource = TableResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('List of :label', ['label' => __('table')]);
    }

    public function getTitle(): string
    {
        return __('List of :label', ['label' => __('table')]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl')
                ->icon('heroicon-m-plus'),
        ];
    }
}

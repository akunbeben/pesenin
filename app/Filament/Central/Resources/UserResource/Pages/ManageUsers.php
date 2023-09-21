<?php

namespace App\Filament\Central\Resources\UserResource\Pages;

use App\Filament\Central\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    return [
                        ...$data,
                        'password' => Str::random(),
                        'email_verified_at' => now()->toDateTimeString(),
                        'require_reset' => true,
                    ];
                })
                ->createAnother(false)
                ->icon('heroicon-m-plus')
                ->modalWidth('xl'),
        ];
    }
}

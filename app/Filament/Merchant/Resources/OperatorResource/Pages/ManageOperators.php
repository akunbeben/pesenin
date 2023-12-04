<?php

namespace App\Filament\Merchant\Resources\OperatorResource\Pages;

use App\Filament\Merchant\Resources\OperatorResource;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ManageOperators extends ManageRecords
{
    protected static string $resource = OperatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                return [
                    ...$data,
                    'password' => Str::random(),
                    'email_verified_at' => now()->toDateTimeString(),
                    'require_reset' => true,
                    'employee_of' => Filament::getTenant()->getKey(),
                ];
            })
                ->after(function (User $record) {
                    $record->sendPasswordResetNotification(Password::getRepository()->create($record));
                })
                ->createAnother(false)
                ->icon('heroicon-m-plus')
                ->modalWidth('xl'),
        ];
    }
}

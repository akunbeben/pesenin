<?php

namespace App\Filament\Central\Resources\UserResource\Pages;

use App\Filament\Central\Resources\UserResource;
use App\Jobs\RegisterDestinationAddress;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Password;
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
                ->after(function (User $record) {
                    $record->sendPasswordResetNotification(Password::getRepository()->create($record));
                    RegisterDestinationAddress::dispatch($record);
                })
                ->createAnother(false)
                ->icon('heroicon-m-plus')
                ->modalWidth('xl'),
        ];
    }
}

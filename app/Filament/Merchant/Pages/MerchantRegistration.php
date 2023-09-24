<?php

namespace App\Filament\Merchant\Pages;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant as Page;

class MerchantRegistration extends Page
{
    public static function getLabel(): string
    {
        return __('Register merchant');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->autofocus()
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label(__('Phone'))
                    ->maxLength(13)
                    ->tel()
                    ->telRegex('/^0[8][1-9]\d{1}[\s-]?\d{4}[\s-]?\d{2,5}$/')
                    ->helperText(__('Example: 081234567890'))
                    ->unique()
                    ->required(),
                Forms\Components\Textarea::make('address')
                    ->label(__('Address'))
                    ->required(),
                Forms\Components\TextInput::make('city')
                    ->label(__('City'))
                    ->required(),
                Forms\Components\TextInput::make('zip')
                    ->label(__('Zip Code'))
                    ->required(),
                Forms\Components\TextInput::make('country')
                    ->label(__('Country'))
                    ->required(),
            ]);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }
}

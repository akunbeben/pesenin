<?php

namespace App\Filament\Merchant\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile as Page;

class MerchantProfile extends Page
{
    public static function getLabel(): string
    {
        return __('Merchant setting');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()->schema([
                    Forms\Components\Tabs\Tab::make(__('Merchant detail'))->schema([
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
                            ->required(),
                        Forms\Components\Textarea::make('address')
                            ->label(__('Address'))
                            ->required(),
                    ]),
                ]),
            ]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('Merchant detail saved');
    }
}

<?php

namespace App\Filament\Merchant\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile as Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
                Forms\Components\Tabs::make()
                    ->persistTabInQueryString()
                    ->schema([
                        Forms\Components\Tabs\Tab::make(__('Merchant detail'))
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
                                    ->unique(ignorable: $this->tenant)
                                    ->required(),
                                Forms\Components\Textarea::make('address')
                                    ->label(__('Address'))
                                    ->required(),
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('city')
                                        ->label(__('City'))
                                        ->required(),
                                    Forms\Components\TextInput::make('zip')
                                        ->label(__('Zip Code'))
                                        ->required(),
                                    Forms\Components\TextInput::make('country')
                                        ->label(__('Country'))
                                        ->required(),
                                ]),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('Miscellaneous'))
                            ->statePath('setting')
                            ->schema([
                                Forms\Components\Toggle::make('cash_mode')
                                    ->helperText(__('You can activate it to receive cash payment from customers')),
                                Forms\Components\Toggle::make('ikiosk_mode')
                                    ->helperText(__('The default system uses the \'Multiple-tables QRCode ordering\' schema. If you have an iKiosk device, you can activate it, and it will switch to a single QRCode instance')),
                            ]),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use (&$record, $data) {
            /** @var \App\Models\Merchant $record */
            $record->update($data);

            $record->setting()->updateOrCreate([], $data['setting']);

            return $record;
        });
    }

    protected function fillForm(): void
    {
        $data = $this->tenant->load(['setting'])->toArray();

        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('Merchant detail saved');
    }
}

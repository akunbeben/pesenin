<?php

namespace App\Filament\Merchant\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile as Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;

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
                        Forms\Components\Tabs\Tab::make(__('Feature settings'))
                            ->statePath('setting')
                            ->schema([
                                Forms\Components\Toggle::make('cash_mode')
                                    ->hidden()
                                    ->label(__('Accept cash payment'))
                                    ->helperText(__('You have the option to activate it, allowing you to seamlessly receive cash payments from customers.')),
                                Forms\Components\Toggle::make('ikiosk_mode')
                                    ->label(__('iKiosk mode'))
                                    ->helperText(__('The standard system employs the \'Multiple-tables QRCode ordering\' structure. If you possess an iKiosk device, activating it will cause a transition to a singular QRCode instance.')),
                                Forms\Components\Toggle::make('tax')
                                    ->label(__('PPN 11%'))
                                    ->helperText(__('With this option active, PPN 11% tax will be included in the total amount charged to the customer.')),
                                Forms\Components\Toggle::make('fee')
                                    ->label(__('Admin fee 4%'))
                                    ->helperText(__('With this option active, a 4% payment gateway fee will be included in the total amount charged to the customer.')),
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

            if ((bool) $data['setting']['ikiosk_mode']) {
                $record->tables()->getQuery()->forceDelete();

                $record->tables()->create([
                    'number' => 1,
                    'seats' => 0,
                ]);
            }

            foreach (['ikiosk' => 'ikiosk_mode', 'tax' => 'tax', 'fee' => 'fee'] as $key => $value) {
                Feature::for($record)->activate($key, $data['setting'][$value]);
            }

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

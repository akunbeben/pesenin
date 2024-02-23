<?php

namespace App\Filament\Merchant\Pages;

use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
                                SpatieMediaLibraryFileUpload::make('avatar')
                                    ->label(__('Merchant picture'))
                                    ->collection('avatar')
                                    ->multiple(false),
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
                                    ->unique(ignorable: $this->tenant),
                                Forms\Components\Textarea::make('address')
                                    ->label(__('Address')),
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('city')
                                        ->label(__('City')),
                                    Forms\Components\TextInput::make('zip')
                                        ->label(__('Zip Code')),
                                    Forms\Components\TextInput::make('country')
                                        ->label(__('Country')),
                                ]),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('Feature settings'))
                            ->statePath('setting')
                            ->hidden(! Feature::for($this->tenant)->active('feature_payment'))
                            ->schema([
                                Forms\Components\Toggle::make('ikiosk_mode')
                                    ->hidden() // disabled
                                    ->label(__('iKiosk mode'))
                                    ->helperText(__('If you have a device that is intended as an IKIOSK device, you can turn this feature on.')),
                                Forms\Components\Toggle::make('tax')
                                    ->label(__('Charge PPN 11% to customers'))
                                    ->helperText(__('With this option active, PPN 11% tax will be included in the total amount charged to the customer.')),
                                Forms\Components\Toggle::make('fee')
                                    ->label(__('Charge service fees to customers (payment gateway)*'))
                                    ->helperText(__('With this option active, a 4% payment gateway fee will be included in the total amount charged to the customer.')),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('Payment channels'))
                            ->statePath('channels')
                            ->hidden(! Feature::for($this->tenant)->active('feature_payment'))
                            ->schema([
                                Forms\Components\Toggle::make('cash_mode')
                                    // ->hidden() // disabled
                                    ->label(__('Cash')),
                                Forms\Components\Toggle::make('qris')
                                    // ->hidden() // disabled
                                    ->label(__('QRIS')),
                                Forms\Components\Toggle::make('e-wallet')
                                    // ->hidden() // disabled
                                    ->label(__('E-Wallet')),
                            ]),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::beginTransaction();

        try {
            /** @var \App\Models\Merchant $record */
            $record->update($data);

            $record->setting()->updateOrCreate([], $data['setting']);

            foreach (['feature_ikiosk' => 'ikiosk_mode', 'feature_tax' => 'tax', 'feature_fee' => 'fee'] as $key => $value) {
                Feature::for($record)->activate($key, $data['setting'][$value]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            if (! app()->isProduction()) {
                throw $th;
            }

            logger()->error($th->getMessage(), $th->getTrace());

            Notification::make()
                ->title(app()->isProduction() ? __('Order success') : $th->getMessage())
                ->body(app()->isProduction() ? __('Your ordered items will be delivered to you as soon as possible') : $th->getTraceAsString())
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        DB::commit();

        return $record;
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

<?php

namespace App\Filament\Merchant\Pages;

use App\Http\Middleware\EnsureNotEmployee;
use App\Jobs\ForwardingEmail;
use App\Models\Merchant;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant as Page;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MerchantRegistration extends Page
{
    public static function getLabel(): string
    {
        return __('Register merchant');
    }

    public static function getRouteMiddleware(Panel $panel): string | array
    {
        return [
            ...parent::getRouteMiddleware($panel),
            EnsureNotEmployee::class,
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->autofocus()
                    ->required(),
                Forms\Components\Textarea::make('address')
                    ->label(__('Address')),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('phone')
                        ->label(__('Phone'))
                        ->maxLength(13)
                        ->tel()
                        ->telRegex('/^0[8][1-9]\d{1}[\s-]?\d{4}[\s-]?\d{2,5}$/')
                        ->unique(Merchant::class)
                        ->helperText(__('Example: 081234567890')),
                    Forms\Components\TextInput::make('city')
                        ->label(__('City')),
                    Forms\Components\TextInput::make('zip')
                        ->label(__('Zip Code')),
                    Forms\Components\TextInput::make('country')
                        ->label(__('Country')),
                ]),
                Forms\Components\ToggleButtons::make('payment_gateway')
                    ->options([
                        0 => 'Disable',
                        1 => 'Enable',
                    ])
                    ->icons([
                        0 => 'heroicon-o-x-circle',
                        1 => 'heroicon-o-check-circle',
                    ])
                    ->colors([
                        0 => 'danger',
                        1 => 'success',
                    ])
                    ->required()
                    ->inline(),
            ]);
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['user_id'] = Filament::auth()->id();

        return $data;
    }

    protected function handleRegistration(array $data): Model
    {
        try {
            DB::beginTransaction();

            $merchant = Merchant::query()->create($data);

            $merchant->setting()->create();

            ForwardingEmail::dispatch($merchant->user, $merchant, (bool) $data['payment_gateway']);
        } catch (\Throwable $th) {
            DB::rollBack();

            if (app()->isLocal()) {
                throw $th;
            }

            logger()->error($th->getMessage(), $th->getTrace());
        }

        DB::commit();

        return $merchant;
    }
}

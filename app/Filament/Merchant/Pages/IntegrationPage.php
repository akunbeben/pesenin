<?php

namespace App\Filament\Merchant\Pages;

use App\Events\SyncToPawoon;
use App\Forms\Components\CustomLink;
use App\Services\Pawoon\Service;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class IntegrationPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Integration';

    protected static ?int $navigationSort = 9999;

    protected static string $view = 'filament.merchant.pages.integration-page';

    #[Locked]
    public array $services;

    public ?array $outlets = null;

    public ?string $selectedOutlet = null;

    public static function canAccess(): bool
    {
        return Feature::for(Filament::getTenant())->active('feature_payment');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Integrate to your POS');
    }

    public function mount(): void
    {
        $this->services = [
            'pawoon' => [
                'name' => 'Pawoon',
                'url' => 'https://dashboard.pawoon.com/integration/apply/oauth/',
            ],
        ];

        if (Filament::getTenant()->integration && !Filament::getTenant()->external_id) {
            $this->dispatch('get-outlets');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('moka')
                ->hidden(!Filament::getTenant()->integration && !Filament::getTenant()->external_id)
                ->requiresConfirmation()
                ->size('lg')
                ->label(__('Sync Products'))
                ->icon('heroicon-o-arrow-path')
                ->modalIcon('heroicon-o-arrow-path')
                ->color('gray')
                ->successNotificationTitle(__('Data syncronization is processed in the background'))
                ->action(function (Actions\Action $action) {
                    SyncToPawoon::dispatch(Filament::getTenant());

                    $action->success();
                }),
        ];
    }

    public function pawoon(): Actions\Action
    {
        return Actions\Action::make('pawoon')
            ->size('lg')
            ->label(__('Integrate now'))
            ->requiresConfirmation()
            ->modalWidth('lg')
            ->form([
                Forms\Components\TextInput::make('client_id')
                    ->required()
                    ->label(__('Client ID')),
                Forms\Components\TextInput::make('client_secret')
                    ->required(),
                CustomLink::make(__('Get client ID and client secret from this url'))
                    ->formatStateUsing(fn () => 'https://dashboard.pawoon.com/integration/apply/oauth')
                    ->hiddenLabel(),
            ])
            ->action(function (array $data, Actions\Action $action) {
                /** @var \App\Models\Merchant $merchant */
                $merchant = Filament::getTenant();

                DB::beginTransaction();

                try {
                    $merchant->integration()->updateOrCreate(['merchant_id' => $merchant->getKey()], [
                        ...$data,
                        'provider' => 'pawoon',
                    ]);

                    Service::new($merchant->integration)->connect();
                } catch (\Throwable $th) {
                    DB::rollBack();
                    logger(null)->error($th->getMessage());

                    if (!app()->isProduction()) {
                        throw $th;
                    }

                    $action->failure();
                }

                DB::commit();
                $action->success();
            })
            ->modalIcon(static::$navigationIcon)
            ->after(fn (self $livewire) => $livewire->dispatch('get-outlets'));
    }

    #[On('save-outlet')]
    public function saveOutlet(): void
    {
        if (!$this->selectedOutlet) {
            return;
        }

        try {
            ['id' => $id, 'name' => $name] = Crypt::decrypt($this->selectedOutlet);

            Filament::getTenant()->update([
                'external_id' => $id,
                'external_name' => $name,
            ]);
        } catch (\Throwable $th) {
            logger(null)->error($th->getMessage());

            if (!app()->isProduction()) {
                throw $th;
            }

            Notification::make()
                ->title(__('Integration failed'))
                ->body(__('Pawoon POS integration failed, please try again'))
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('Integration success'))
            ->body(__('Successfully integrated to Pawoon POS'))
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'select-outlet');
    }

    public function moka(): Actions\Action
    {
        return Actions\Action::make('moka')
            ->size('lg')
            ->label(__('Integrate now'))
            ->requiresConfirmation();
    }

    #[On('get-outlets')]
    public function listOutlets(): void
    {
        /** @var \App\Models\Integration $integration */
        $integration = Filament::getTenant()->integration;

        $this->outlets = match ($integration->provider) {
            'pawoon' => Service::make($integration)->outlets(),
            default => [],
        };

        $this->dispatch('open-modal', id: 'select-outlet');
    }
}

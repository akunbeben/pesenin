<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Table;
use App\Traits\Tables\QRStatus;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Widgets\Widget;

class QRCode extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.merchant.widgets.q-r-code';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 6,
        'sm' => 2,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
        '2xl' => 2,
    ];

    public Table $table;

    protected function getColumns(): int
    {
        return 2;
    }

    public static function isDiscovered(): bool
    {
        return (bool) Filament::getTenant()?->business_id;
    }

    public function visitAction(): Action
    {
        return Action::make('open_url')
            ->size(ActionSize::Large)
            ->label(__('Open'))
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->extraAttributes(['class' => 'w-full'])
            ->outlined()
            ->url($this->table->url, true);
    }

    public function downloadAction(): Action
    {
        $media = $this->table->getFirstMedia('qr');

        return Action::make('download')
            ->size(ActionSize::Large)
            ->icon('heroicon-o-arrow-down-on-square')
            ->extraAttributes(['class' => 'w-full'])
            ->outlined()
            ->action(fn () => response()->download(
                $media->getPath(),
                str($this->table->merchant->name)->slug(),
                [
                    'Content-Type' => 'image/png',
                    'Content-Length' => $media->size,
                ]
            ));
    }

    public function mount(): void
    {
        /** @var \App\Models\Merchant $merchant */
        $merchant = Filament::getTenant();

        $this->table = $merchant->tables()->withoutGlobalScope('owned')->firstOrCreate([
            'number' => 0,
            'seats' => 0,
        ]);

        if (!$this->table->getFirstMedia('qr')) {
            /** @var \SimpleSoftwareIO\QrCode\Generator $service */
            $service = app(\SimpleSoftwareIO\QrCode\Generator::class);

            $this->table->addMediaFromBase64(base64_encode(
                $service->format('png')
                    ->margin(2)
                    ->size(1000)
                    ->generate($this->table->url)
            ))->toMediaCollection('qr');

            $this->table->update(['qr_status' => QRStatus::Generated]);

            $this->dispatch('$refresh()');
        }
    }
}

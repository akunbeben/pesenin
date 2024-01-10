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
use Filament\Widgets\Widget;
use Laravel\Pennant\Feature;

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

    public static function isDiscovered(): bool
    {
        return Feature::for(Filament::getTenant())->active('feature_ikiosk');
    }

    public function visitAction(): Action
    {
        return Action::make('open_url')
            ->label(__('Test transaction'))
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->extraAttributes(['class' => 'w-full'])
            ->outlined()
            ->url($this->table->url, true);
    }

    public function downloadAction(): Action
    {
        $media = $this->table->getFirstMedia('qr');

        return Action::make('download')
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
        $this->table = Filament::getTenant()->tables->first();

        if (! $this->table->getFirstMedia('qr')) {
            /** @var \SimpleSoftwareIO\QrCode\Generator $service */
            $service = app(\SimpleSoftwareIO\QrCode\Generator::class);

            $this->table->addMediaFromBase64(base64_encode(
                $service->format('png')
                    ->margin(2)
                    ->size(1000)
                    ->generate($this->table->url)
            ))->toMediaCollection('qr');

            $this->table->update(['qr_status' => QRStatus::Generated]);
        }
    }
}

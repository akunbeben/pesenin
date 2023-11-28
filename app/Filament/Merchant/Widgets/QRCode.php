<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Table;
use App\Traits\Tables\QRStatus;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class QRCode extends Widget
{
    protected static string $view = 'filament.merchant.widgets.q-r-code';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 2;

    public Table $table;

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

<?php

namespace App\Filament\Merchant\Resources\TableResource\Pages;

use App\Filament\Merchant\Resources\TableResource;
use App\Traits\Tables\QRStatus;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewTable extends ViewRecord
{
    protected static string $resource = TableResource::class;

    public function getHeading(): string | Htmlable
    {
        return __('Detail :label', ['label' => __('table')]);
    }

    public function getTitle(): string
    {
        return __('Detail :label', ['label' => __('table')]);
    }

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        if (!$this->hasInfolist()) {
            $this->fillForm();
        }

        /** @var \App\Models\Table $table */
        $table = $this->record;

        if (!$table->getFirstMedia('qr')) {
            /** @var \SimpleSoftwareIO\QrCode\Generator $service */
            $service = app(\SimpleSoftwareIO\QrCode\Generator::class);

            $table->addMediaFromBase64(base64_encode(
                $service->format('png')
                    ->margin(2)
                    ->size(1000)
                    ->generate($table->url)
            ))->toMediaCollection('qr');

            $table->update(['qr_status' => QRStatus::Generated]);
        }
    }
}

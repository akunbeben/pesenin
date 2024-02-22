<?php

namespace App\Filament\Outlet\Pages;

use App\Models\Order;
use App\Traits\Orders\Status;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class Scan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.outlet.pages.scan';

    #[On('scanned')]
    public function scanValidation(string $ref): void
    {
        $order = Order::query()->firstWhere('number', $ref);

        if (! $order) {
            Notification::make()
                ->title(__('Invalid QR'))
                ->body(__('Please scan correct QR from the customer\'s receipt.'))
                ->danger()
                ->send();

            $this->halt();
        }

        try {
            $order->update(['status' => Status::Success]);
        } catch (\Throwable $th) {
            if (! app()->isProduction()) {
                throw $th;
            }

            logger()->error($th->getMessage(), $th->getTrace());

            Notification::make()
                ->title(app()->isProduction() ? __('Payment confirmation failed') : $th->getMessage())
                ->body(app()->isProduction() ? __('Please try again to scan the QRCode') : $th->getTraceAsString())
                ->danger()
                ->send();

            $this->dispatch('failed');

            $this->halt();
        }

        Notification::make()
            ->title($order->number)
            ->body(__('Payment confirmation success.'))
            ->success()
            ->send();

        $this->dispatch('resume');
    }
}

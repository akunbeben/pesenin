<?php

namespace App\Filament\Outlet\Pages;

use App\Models\Order;
use App\Traits\Orders\Serving;
use App\Traits\Orders\Status;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class OrderInQueues extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-m-queue-list';

    protected static string $view = 'filament.outlet.pages.order-in-queues';

    public Collection $waiting;

    public Collection $processed;

    public Collection $completed;

    public function getHeading(): string | Htmlable
    {
        return __('Queues');
    }

    public function getTitle(): string
    {
        return __('Order in queues');
    }

    public static function getNavigationLabel(): string
    {
        return __('Order in queues');
    }

    public function loads(): void
    {
        $this->waiting = Order::query()->with(['items', 'scan.table'])
            ->orderBy('queued_at', 'asc')
            ->whereNotIn('serving', [Serving::NotReady, Serving::Finished])
            ->whereNotIn('status', [Status::Pending, Status::Manual, Status::Expired])
            ->where('serving', Serving::Waiting)
            ->get();

        $this->processed = Order::query()->with(['items', 'scan.table'])
            ->orderBy('queued_at', 'asc')
            ->whereNotIn('serving', [Serving::NotReady, Serving::Finished])
            ->whereNotIn('status', [Status::Pending, Status::Manual, Status::Expired])
            ->where('serving', Serving::Processed)
            ->get();

        $this->completed = Order::query()->with(['items', 'scan.table'])
            ->latest('queued_at')
            ->whereNotIn('serving', [Serving::NotReady, Serving::Finished])
            ->whereNotIn('status', [Status::Pending, Status::Manual, Status::Expired])
            ->where('serving', Serving::Completed)
            ->get();
    }

    #[On('scanned')]
    public function scanValidation(string $ref): void
    {
        $order = Order::query()->with('payment')->where('number', $ref)->first();

        if (! $order) {
            Notification::make()
                ->title(__('Invalid QR'))
                ->body(__('Please scan correct QR from the customer\'s receipt.'))
                ->danger()
                ->send();

            $this->halt();
        }

        DB::beginTransaction();

        try {
            $order->update([
                'status' => Status::Success,
                'serving' => Serving::Waiting,
                'queued_at' => now(),
            ]);

            $order->payment->update([
                'data' => array_merge((array) $order->payment->data, [
                    'status' => 'PAID',
                    'paid_at' => now(),
                ]),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            if (! app()->isProduction()) {
                throw $th;
            }

            logger(null)->error($th->getMessage(), $th->getTrace());

            Notification::make()
                ->title(app()->isProduction() ? __('Payment confirmation failed') : $th->getMessage())
                ->body(app()->isProduction() ? __('Please try again to scan the QRCode') : $th->getTraceAsString())
                ->danger()
                ->send();

            $this->dispatch('failed');

            $this->halt();
        }

        DB::commit();
        Notification::make()
            ->title($order->number)
            ->body(__('Payment confirmation success.'))
            ->success()
            ->send();

        $this->dispatch('resume');
    }

    #[On('forward')]
    public function next(Order $order): void
    {
        $order->process();
        $this->loads();
    }

    #[On('backward')]
    public function prev(Order $order): void
    {
        $order->process(false);

        $this->loads();
    }

    public function mount(): void
    {
        $this->loads();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('scan_receipt')
                ->color('gray')
                ->modalWidth('md')
                ->translateLabel()
                ->modalSubmitAction(Action::make()->hidden())
                ->modalCancelAction(Action::make()->hidden())
                ->icon('heroicon-o-qr-code')
                ->tooltip(__('Scan receipt to confirm cash/manual payment'))
                ->modalContent(fn () => view('filament.outlet.components.camera')),
        ];
    }
}

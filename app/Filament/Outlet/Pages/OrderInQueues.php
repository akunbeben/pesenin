<?php

namespace App\Filament\Outlet\Pages;

use App\Models\Order;
use App\Traits\Orders\Serving;
use App\Traits\Orders\Status;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class OrderInQueues extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-queue-list';

    protected static string $view = 'filament.outlet.pages.order-in-queues';

    public Collection $waiting;

    public Collection $processed;

    public Collection $completed;

    public function getHeading(): string | Htmlable
    {
        return __('');
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
}

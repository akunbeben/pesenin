<?php

namespace App\Livewire;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Livewire\Component;

class Summary extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Order $order;

    public function cancelAction(): Action
    {
        return Action::make('cancel')
            ->size(ActionSize::Large)
            ->extraAttributes(['class' => 'w-full'])
            ->color('gray')
            ->requiresConfirmation()
            ->successNotificationTitle(__('Your order has been canceled'))
            ->label(__('Cancel order'))
            ->action(fn () => $this->order->cancel());
    }

    public function mount(Order $order): void
    {
        $this->order = $order->load(['items']);
    }

    public function render()
    {
        return view('livewire.summary');
    }
}

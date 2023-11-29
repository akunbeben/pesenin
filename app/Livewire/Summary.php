<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class Summary extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.summary');
    }
}

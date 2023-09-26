<?php

namespace App\Services\Tables;

use App\Models\Table;
use Illuminate\Support\Collection;
use Livewire\Component;

class QRGenerator
{
    public Component $component;

    public Table | Collection $subject;

    public function handle(Component $component, Table | Collection $subject): void
    {
        $this->component = $component;
        $this->subject = $subject;

        if ($subject instanceof Collection) {
            $this->bulk();

            return;
        }

        dd($this->component, $this->subject);
    }

    private function bulk(): void
    {
        dd($this->component, $this->subject, 'bulk');
    }
}

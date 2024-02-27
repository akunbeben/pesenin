<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Scanner extends Component
{
    #[On('check-payload')]
    public function validation(string $payload): void
    {
        $validURL = config('app.url');

        if (str(trim($payload))->startsWith($validURL)) {
            $this->redirect(trim($payload));
        }
    }

    public function render()
    {
        return view('livewire.scanner');
    }
}

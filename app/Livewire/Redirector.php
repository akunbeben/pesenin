<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;

class Redirector extends Component
{
    public function mount(Request $request): void
    {
        dd($request->get(\Linkeys\UrlSigner\Models\Link::class));
    }

    public function render()
    {
        return <<<'HTML'
        <div class="flex items-center justify-center min-h-screen sm:max-w-sm md:max-w-3xl">
            <span>Loading</span>
        </div>
        HTML;
    }
}

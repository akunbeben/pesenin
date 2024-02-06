<?php

namespace App\Livewire;

use App\Models\Scan;
use App\Models\Table;
use App\Support\Encoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Sqids\Sqids;

class Choose extends Component
{
    #[Locked]
    public Scan $scan;

    #[Locked]
    public string $scanId;

    #[Locked]
    public string $u;

    #[Locked]
    public string $forward;

    public function mount(string $scanId, Request $request): void
    {
        abort_if(blank($request->u), 404);

        [$id] = Encoder::decode($this->u = $request->u, $this->scanId = $scanId);

        $this->scan = Scan::query()->findOrFail($id);

        // $this->forward = route('browse', [
        //     'u' => $salt = Encoder::shuffle(Crypt::encrypt(now()->timestamp), 10),
        //     'scanId' => (new Sqids($salt, 5))->encode([$scan->getKey(), 1]),
        // ]);
    }

    #[On('choose-table')]
    public function chooseTable(Table $table): void
    {
        dd($table);
    }

    public function render()
    {
        return view('livewire.choose', [
            'tables' => Table::query()->get(),
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Table;
use App\Support\Encoder;
use App\Traits\Fingerprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Laravel\Pennant\Feature;
use Livewire\Component;
use Sqids\Sqids;

class Redirector extends Component
{
    use Fingerprint;

    private string $url;

    protected Table $table;

    public function mount(Request $request): void
    {
        $this->table = Table::query()->with(['scans' => function ($query) {
            $query->latest();
        }])->where('uuid', $request->uid)->firstOrFail();

        /** @var \App\Models\Scan $scan */
        $scan = $this->table->scans()->create([
            'agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'fingerprint' => $this->fingerprint(),
        ]);

        $this->url = route(match (Feature::for($this->table->merchant)->active('feature_ikiosk')) {
            true => 'choose',
            false => 'browse',
        }, [
            'u' => $salt = Encoder::shuffle(Crypt::encrypt(now()->timestamp), 10),
            'scanId' => (new Sqids($salt, 5))->encode([$scan->getKey(), 0]),
        ]);
    }

    public function rendered(): void
    {
        $this->redirect($this->url, true);
    }

    public function render()
    {
        return <<<'HTML'
        <div class="flex flex-col items-center justify-center min-h-screen gap-5 mx-auto text-gray-700 sm:max-w-sm md:max-w-3xl">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 animate-spin">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span id="loading" class="w-20">Loading</span>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const loadingText = document.getElementById("loading");

                    function updateLoadingText() {
                        loadingText.textContent += ".";

                        if (loadingText.textContent.length > 10) {
                            loadingText.textContent = "Loading";
                        }
                    }

                    setInterval(updateLoadingText, 500);
                });
            </script>
        </div>
        HTML;
    }
}

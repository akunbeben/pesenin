<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Scan;
use App\Models\Table;
use Detection\MobileDetect;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Browse extends Component
{
    use WithPagination;

    #[Url]
    public ?string $search = null;

    #[Url]
    public ?string $tab = null;

    public Scan $scan;

    public Table $table;

    public ?Product $showed;

    public Collection $cart;

    public function updatedTab(): void
    {
        $this->resetPage();

        if ($this->table->merchant->categories->contains('hashed', $this->tab)) {
            return;
        }

        $this->tab = null;
    }

    #[On('show-product')]
    public function showProduct(Product $product): void
    {
        $this->showed = $product;

        $this->dispatch('open-modal', id: 'product-detail');
    }

    #[On('close-modal')]
    public function closeProduct(): void
    {
        $this->showed = null;
    }

    #[On('add-to-cart')]
    public function addToCart(Product $product): void
    {
        if (! $this->cart->contains($product)) {
            $this->cart->push($product);
        }

        $this->dispatch('close-modal', id: 'product-detail');
    }

    #[On('view-cart')]
    public function viewCart(): void
    {
        $this->dispatch('open-modal', id: 'my-cart');
    }

    public function mount(string $scanId, Request $request): void
    {
        abort_if(blank($request->u), 404);

        $this->scan = Scan::query()->findOrFail(
            Arr::first((new Hashids($request->u, 5))->decode($scanId))
        );

        abort_if($this->scan->finished, 403, 'Please rescan the QRCode');

        $this->cart = collect([]);
        $this->table = $this->scan->table;
    }

    public function render()
    {
        return view('livewire.browse', [
            'products' => $this->table->merchant->products()->available()->when($this->tab, function (Builder $query) {
                if (! $this->table->merchant->categories->contains('hashed', $this->tab)) {
                    $this->tab = '';

                    return;
                }

                $id = (new Hashids(config('app.key'), 3))->decode($this->tab)[0];

                $query->where('category_id', $id);
            })->search($this->search)
                ->orderBy('recommended', 'desc')
                ->orderBy('name', 'asc')
                ->orderBy('created_at', 'desc')
                ->simplePaginate(
                    ($detect = new MobileDetect())->isMobile() ? ($detect->isTablet() ? 12 : 6) : 12
                ),
            'highlights' => $this->table->merchant->products()->highlights()->get(),
            'categories' => $this->table->merchant->categories,
        ]);
    }
}

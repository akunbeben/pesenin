<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Table;
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

    public bool $showModal = false;

    public Table $table;

    public Product $showed;

    public Collection $selected;

    #[On('show-product')]
    public function showProduct(Product $product): void
    {
        $this->showed = $product;

        $this->dispatch('open-modal', id: 'product-detail');
    }

    #[On('add-to-cart')]
    public function addToCart(Product $product): void
    {
        if (! $this->selected->contains($product)) {
            $this->selected->push($product);
        }

        $this->dispatch('close-modal', id: 'product-detail');
    }

    #[On('view-cart')]
    public function viewCart(): void
    {
        $this->dispatch('open-modal', id: 'my-cart');
    }

    public function mount(Table $table): void
    {
        $this->selected = collect([]);
        $this->table = $table;
    }

    public function render()
    {
        return view('livewire.browse', [
            'products' => $this->table->merchant->products()->paginate(),
        ]);
    }
}
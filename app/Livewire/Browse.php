<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Scan;
use App\Models\Table;
use App\Support\Encoder;
use Detection\MobileDetect;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Sqids\Sqids;

class Browse extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use WithPagination;

    #[Url]
    public ?string $search = null;

    #[Url]
    public ?string $tab = null;

    public Scan $scan;

    public string $scanId;

    public string $u;

    public Table $table;

    public ?Product $showed;

    public Collection $cart;

    public ?string $variant = null;

    public function tableInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->table)
            ->schema([
                Section::make(fn (Table $record) => $record->merchant->name)
                    ->description(__('Tap to see more details'))
                    ->columns(['default' => 2])
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('name')->label(__('Table'))->columnSpan(1),
                        TextEntry::make('seats')->translateLabel()->columnSpan(1),
                        TextEntry::make('merchant.name')->translateLabel()->columnSpan(2),
                    ]),
            ]);
    }

    public function updatedTab(): void
    {
        $this->resetPage();

        if (
            $this->table->merchant->categories->filter(
                fn (Category $category) => $category->reverse($this->tab, $this->u)
            )->isNotEmpty()
        ) {
            return;
        }

        $this->tab = null;
    }

    #[On('select-variant')]
    public function selectVariant(string $variant): void
    {
        $this->variant = $variant;
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
        $this->variant = null;
    }

    #[On('add-to-cart')]
    public function addToCart(Product $product): void
    {
        if ($this->cart->where('product_id', $product->getKey())->isEmpty()) {
            $this->cart->push([
                'product_id' => $product->getKey(),
                'snapshot' => $product->toArray(),
                'variant' => $this->variant,
                'amount' => 1,
            ]);
        }

        $this->dispatch('close-modal', id: 'product-detail');
    }

    #[On('increase-item')]
    public function increase(Product $product): void
    {
        $this->cart = $this->cart->transform(function ($item) use ($product) {
            if ($item['product_id'] !== $product->getKey()) {
                return $item;
            }

            $item['amount'] += 1;

            return $item;
        })->filter();
    }

    #[On('decrease-item')]
    public function decrease(Product $product): void
    {
        $this->cart = $this->cart->transform(function ($item) use ($product) {
            if ($item['product_id'] !== $product->getKey()) {
                return $item;
            }

            if ($item['amount'] <= 1) {
                return null;
            }

            $item['amount'] -= 1;

            return $item;
        })->filter();
    }

    #[On('view-cart')]
    public function viewCart(): void
    {
        $this->js(<<<'JS'
            console.log($wire.cart)
        JS);

        $this->dispatch('open-modal', id: 'my-cart');
    }

    public function mount(string $scanId, Request $request): void
    {
        abort_if(blank($request->u), 404);

        $this->scan = Scan::query()->findOrFail(Encoder::decode(
            $this->u = $request->u,
            $this->scanId = $scanId,
        ));

        abort_if($this->scan->created_at->diffInHours() > 1, 403, 'Please rescan the QRCode');
        abort_if($this->scan->finished, 403, 'Please rescan the QRCode');
        abort_if(!$this->scan->table, 403, 'Please rescan the QRCode');

        $this->cart = collect([]);
        $this->table = $this->scan->table;
    }

    public function render()
    {
        return view('livewire.browse', [
            'products' => $this->table->merchant->products()->available()->when($this->tab, function (Builder $query) {
                if (
                    $this->table->merchant->categories->filter(
                        fn (Category $category) => $category->reverse($this->tab, $this->u)
                    )->isEmpty()
                ) {
                    $this->tab = null;

                    return;
                }

                $id = Arr::first((new Sqids($this->u, 5))->decode($this->tab));

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

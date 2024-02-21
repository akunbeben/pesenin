<div
    id="grid"
    @class([
        'grid grid-cols-2 md:grid-cols-4 gap-y-3.5 gap-x-2.5 py-2',
        'mb-7' => $this->cart->isNotEmpty(),
    ])
>
    @features('feature_payment', $this->table->merchant)
    <div class="col-span-2 md:col-span-4">
        {{ $this->tableInfolist }}
    </div>
    @endfeatures

    <div class="col-span-2 md:col-span-4">
        <x-filament::input.wrapper suffix-icon="heroicon-m-magnifying-glass">
            <x-filament::input
                placeholder="{{ __('Find your favorite menu ...') }}"
                type="search"
                wire:model.live.debounce.250ms="search"
                class="py-3.5"
            />
        </x-filament::input.wrapper>
    </div>
    <div class="col-span-2 md:col-span-4">
        <x-filament::tabs class="!overflow-auto">
            <x-filament::tabs.item
                wire:click="$set('tab', '')"
                :active="blank($this->tab)"
            >
                {{ __('All') }}
            </x-filament::tabs.item>
            @foreach ($categories as $category)
            <x-filament::tabs.item
                wire:click="$set('tab', '{{ $category->hash($this->u) }}')"
                :active="$category->reverse($this->tab, $this->u)"
            >
                {{ $category->name }}
            </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>
    </div>

    @forelse ($products as $product)
    <div class="relative flex flex-col gap-y-2">
        <div wire:click="$dispatch('show-product', { product: {{ $product->id }} })" class="relative flex flex-col cursor-pointer gap-y-2">
            @if ($product->recommended)
            <span class="absolute px-2 py-1 text-xs rounded-xl left-1.5 top-1.5 bg-primary-500 text-white">Recommended</span>
            @endif
            <img src="{{ $product->getFirstMediaUrl('banner', 'thumbnail') }}" alt="{{ $product->description }}" class="object-cover object-center aspect-square rounded-xl">
            <div class="flex items-center gap-1">
                @if ($this->cart->contains($product))
                <span class="relative inline-flex w-2 h-2 rounded-full bg-primary-500"></span>
                @endif
                <span class="text-xs font-semibold text-gray-950 dark:text-white">{{ $product->name }}</span>
            </div>
            <span class="text-xs text-gray-950 dark:text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
        </div>
        @if (! $this->cart->contains('product_id', $product->getKey()))
            @features('feature_payment', $this->table->merchant)
            <x-filament::button
                outlined
                size="sm"
                x-on:click="() => {
                    if (!!{{ count($product->variants ?? []) }} == true) {
                        $dispatch('show-product', { product: {{ $product->getKey() }} })
                    } else {
                        $dispatch('add-to-cart', { product: {{ $product->getKey() }} })
                    }
                }"
            >
                {{ __('Add') }}
            </x-filament::button>
            @endfeatures
        @else
            @features('feature_payment', $this->table->merchant)
            <div class="flex items-center justify-between">
                <x-filament::button
                    class="aspect-square"
                    size="xs"
                    outlined
                    wire:click="$dispatch('decrease-item', {product: {{ $product->getKey() }}})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                    </svg>
                </x-filament::button>

                <span class="text-sm text-gray-950 dark:text-white">{{ $this->cart->firstWhere('product_id', $product->getKey())['amount'] }}</span>

                <x-filament::button
                    class="aspect-square"
                    size="xs"
                    outlined
                    wire:click="$dispatch('increase-item', {product: {{ $product->getKey() }}})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                    </svg>
                </x-filament::button>
            </div>
            @endfeatures
        @endif
    </div>
    @empty
    <div class="flex flex-col items-center justify-center w-full col-span-2 row-span-2 gap-5 text-gray-500 md:col-span-4 h-80">
        <div class="p-5 bg-gray-300 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
        <span class="px-10 text-base font-semibold text-center">{{ __('We couldn\'t find any products matching your search criteria.') }}</span>
    </div>
    @endforelse

    <div class="col-span-2 md:col-span-4">
        {!! $products->links() !!}
    </div>

    <div class="flex items-center justify-center col-span-2 md:col-span-4 gap-x-1">
        <span class="text-sm md:text-base dark:text-gray-100">Powered by</span>
        <span class="text-sm md:text-base dark:text-gray-100"> &mdash; </span>
        <x-app-logo :class="'!max-w-[100px] sm:!max-w-[150px]'" />
    </div>
</div>
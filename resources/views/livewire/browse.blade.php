<div
    class="max-h-screen px-2 mx-auto overflow-y-auto subpixel-antialiased sm:max-w-sm md:max-w-3xl"
    style="scrollbar-gutter: stable;"
    x-data="{ scrolling: false }"
    x-init="() => {
        $refs.root.addEventListener('scroll', () => {
            scrolling = true;
            setTimeout(() => {
                scrolling = false;
            }, 1000);
        });
    }"
    x-ref="root"
>
    @if ($this->cart->isNotEmpty())
    <div class="fixed bottom-0 left-0 right-0 z-40 flex items-center justify-center w-full px-2 mx-auto sm:max-w-sm md:max-w-3xl">
        <x-filament::button
            wire:click="$dispatch('open-modal', {id: 'my-cart'})"
            class="w-full my-2.5 !rounded-full"
            badgeColor="danger"
            x-show="!scrolling"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
        >
            {{ __('View your order') }}

            <x-slot name="badge">
                {{ $this->cart->sum('amount') }}
            </x-slot>
        </x-filament::button>
    </div>
    @endif

    <div
        id="grid"
        @class([
            'grid grid-cols-2 md:grid-cols-4 gap-y-3.5 gap-x-2.5 py-2',
            'mb-5' => $this->cart->isNotEmpty(),
        ])
    >
        <div class="col-span-2 md:col-span-4">
            {{ $this->tableInfolist }}
        </div>

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
                <img src="{{ $product->getFirstMediaUrl('banner', 'thumbnail') }}" alt="{{ $product->description }}" class="object-cover object-center w-full border-b sm:h-32 h-28 rounded-xl">
                <div class="flex items-center gap-1">
                    @if ($this->cart->contains($product))
                    <span class="relative inline-flex w-2 h-2 rounded-full bg-primary-500"></span>
                    @endif
                    <span class="text-xs font-semibold text-gray-700">{{ $product->name }}</span>
                </div>
                <span class="text-xs text-gray-700">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            </div>
            @if (! $this->cart->contains('product_id', $product->getKey()))
            <x-filament::button
                outlined
                size="xs"
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
            @else
            <div class="flex items-center justify-between">
                <x-filament::button
                    size="xs"
                    outlined
                    wire:click="$dispatch('decrease-item', {product: {{ $product->getKey() }}})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                    </svg>
                </x-filament::button>

                <span class="text-sm">{{ $this->cart->firstWhere('product_id', $product->getKey())['amount'] }}</span>

                <x-filament::button
                    size="xs"
                    outlined
                    wire:click="$dispatch('increase-item', {product: {{ $product->getKey() }}})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                    </svg>
                </x-filament::button>
            </div>
            @endif
        </div>
        @empty
        <div class="flex flex-col items-center justify-center w-full col-span-2 row-span-2 gap-5 text-gray-500 md:col-span-4 h-80">
            <div class="p-5 bg-gray-300 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <span class="text-base font-semibold">{{ __('Not found') }}</span>
        </div>
        @endforelse

        <div class="col-span-2 md:col-span-4">
            {!! $products->links() !!}
        </div>
    </div>

    <x-filament::modal slide-over id="my-cart">
        <div class="flex items-center justify-between">
            <button type="button" class="z-10 p-1 rounded-full top-5 left-5 bg-gray-100/50 md:hidden" x-on:click="$dispatch('close-modal', { id: 'my-cart' })">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            <button type="button" class="z-10 p-1 rounded-full top-5 right-5 bg-gray-100/50" x-on:click="$dispatch('close-modal', { id: 'my-cart' })">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </button>
        </div>

        <div class="flex flex-col p-2 grow">
            @foreach ($cart as $item)
                {{ $item['snapshot']['name'] }}
            @endforeach

            <div class="flex w-full mt-auto bottom-5">
                <x-filament::button
                    class="w-full"
                    size="xl"
                >
                    Checkout
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>

    <x-filament::modal slide-over id="product-detail">
        <div class="relative" x-data="{ f: null }">
            <button type="button" class="fixed z-10 p-1 rounded-full top-5 left-5 bg-gray-100/50 md:hidden" x-on:click="$dispatch('close-modal', { id: 'product-detail' })">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            <button type="button" class="fixed z-10 p-1 rounded-full top-5 right-5 bg-gray-100/50" x-on:click="$dispatch('close-modal', { id: 'product-detail' })">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </button>

            @if ($showed)
                @php
                    $images = $showed?->getMedia('banner')->isNotEmpty()
                        ? $showed?->getMedia('banner')->map(fn ($media) => $media->getUrl())
                        : collect([$showed?->getFirstMediaUrl('banner')]);
                @endphp

                <div
                    x-data="() => {
                        return {
                            active: 0,
                            init() {
                                f = new Flickity(this.$refs.carousel, {
                                    wrapAround: true,
                                    autoPlay: 3000,
                                });

                                f.on('change', i => this.active = i);
                            }
                        }
                    }"
                >
                    <div class="carousel" x-ref="carousel">
                        @foreach ($images as $index => $image)
                        <div
                            class="w-full h-64"
                            x-show="active === parseInt('{{ $index }}')"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-x-12"
                            x-transition:enter-end="opacity-100 transform translate-x-0"
                            x-transition:leave="transition ease-out duration-300"
                            x-transition:leave-start="opacity-100 transform translate-x-0"
                            x-transition:leave-end="opacity-0 transform -translate-x-12"
                        >
                            <img src="{{ $image }}" loading="lazy" class="z-0 object-cover object-center w-full h-64">
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="flex flex-col p-3 grow gap-y-5">
            <span class="text-lg font-semibold">{{ $showed?->name }}</span>
            <p class="text-sm font-normal text-gray-600 line-clamp-5">{{ $showed?->description }}</p>
            <div class="grid gap-2.5 {{ 'grid-cols-' . count($showed?->variants ?? []) }}">
                @forelse ($showed?->variants ?? [] as $variant)
                <button
                    class="relative p-2.5 text-sm flex gap-2.5 justify-center border {{ $this->variant === $variant ? 'border-primary-500 text-primary-500' : 'border-gray-500 text-gray-500' }} rounded-xl"
                    type="button"
                    wire:click="$dispatch('select-variant', {variant: '{{ $variant }}'})"
                >
                    @if ($this->variant === $variant)
                        <span class="absolute flex w-2 h-2 rounded-full top-2 left-2 bg-sky-500"></span>
                    @endif
                    {{ $variant }}
                </button>
                @empty

                @endforelse
            </div>

            <div class="flex w-full mt-auto bottom-5">
                <x-filament::button
                    class="w-full"
                    size="xl"
                    wire:click="$dispatch('add-to-cart', { product: '{{ $showed?->id }}' })"
                >
                    Add to cart
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>
</div>

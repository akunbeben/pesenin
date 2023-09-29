<div class="max-h-screen px-2 mx-auto sm:max-w-sm" x-data="{ showingModal: @entangle('showModal') }">
    <div class="grid grid-cols-2 gap-2.5 py-2">
        <span class="col-span-2 text-lg font-semibold">Recommended products</span>
        <div class="col-span-2" x-data="{ slides: null }" wire:key="{{ rand() }}">
            <div
                x-data="() => {
                    return {
                        active: 0,
                        init() {
                            slides = new Flickity(this.$refs.highlight, {
                                wrapAround: true,
                                autoPlay: 3000,
                            });

                            slides.on('change', i => this.active = i);
                        }
                    }
                }"
                x-ref="highlight"
            >
                @foreach ($highlights as $index => $product)
                <div
                    class="flex w-full justify-between p-2.5 gap-x-2.5 bg-primary-500/40 rounded-xl"
                    x-show.important="active === parseInt('{{ $index }}')"
                >
                    <div class="flex flex-col text-gray-700 grow">
                        <span class="font-semibold text-gray-700">{{ $product->name }}</span>
                        <span class="text-xs">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <x-filament::button x-on:click="$dispatch('view-cart')" class="mt-auto">
                            Add to cart
                        </x-filament::button>
                    </div>
                    <img src="{{ $product->getFirstMediaUrl('banner') }}" alt="{{ $product->description }}" class="object-cover object-center w-20 h-20 border-b sm:w-28 sm:h-28 rounded-xl">
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-span-2">
            <x-filament::input.wrapper suffix-icon="heroicon-m-magnifying-glass">
                <x-filament::input
                    placeholder="Find your favorite menu ..."
                    type="text"
                />
            </x-filament::input.wrapper>
        </div>
        @foreach ($products as $product)
        <div class="bg-white shadow-xs cursor-pointer ring-1 ring-gray-200 rounded-xl" wire:click="$dispatch('show-product', { product: {{ $product->id }} })">
            <img src="{{ $product->getFirstMediaUrl('banner') }}" alt="{{ $product->description }}" class="object-cover object-center w-full border-b sm:h-32 h-28 rounded-t-xl">
            <div class="flex flex-col p-2 gap-y-2.5">
                <span class="text-xs font-semibold">{{ $product->name }}</span>
                <span class="text-xs">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <x-filament::button x-on:click="$dispatch('view-cart')">
        View cart
    </x-filament::button>

    <x-filament::modal slide-over id="my-cart">
        <div class="relative">
            <button type="button" class="fixed z-10 p-1 rounded-full top-5 left-5 bg-gray-100/50 md:hidden" x-on:click="$dispatch('close-modal', { id: 'my-cart' })">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            <button type="button" class="fixed z-10 p-1 rounded-full top-5 right-5 bg-gray-100/50" x-on:click="$dispatch('close-modal', { id: 'my-cart' })">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </button>
        </div>

        @foreach ($cart as $item)
            {{ $item->name }}
        @endforeach
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
                        ? $showed?->getMedia('banner')->map(fn ($media) => $media->original_url)
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
                    class="p-2.5 text-sm flex justify-center border border-gray-500 text-gray-500 rounded-xl"
                    type="button"
                >
                    {{ $variant }}
                </button>
                @empty

                @endforelse
            </div>

            <div class="flex w-full mt-auto bottom-5">
                <x-filament::button
                    class="w-full"
                    size="xl"
                    :icon="$cart->contains($showed) ? 'heroicon-m-check' : 'heroicon-m-shopping-bag'"
                    wire:click="$dispatch('add-to-cart', { product: '{{ $showed?->id }}' })"
                >
                    Add to cart
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>
</div>
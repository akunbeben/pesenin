<div class="max-h-screen px-2 mx-auto sm:max-w-sm" x-data="{ showingModal: @entangle('showModal') }">
    <div class="grid grid-cols-2 gap-2.5 py-2">
        @foreach ($products as $product)
        <div class="bg-white border border-gray-200 shadow-xs cursor-pointer rounded-xl" wire:click="$dispatch('show-product', { product: {{ $product->id }} })">
            <img src="{{ $product->getFirstMediaUrl('banner') }}" alt="{{ $product->description }}" class="object-cover object-center w-full sm:h-32 h-28 rounded-t-xl">
            <div class="p-2">
                <span class="text-xs font-normal">{{ $product->name }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <x-filament::button x-on:click="$dispatch('view-cart')">
        View cart
    </x-filament::button>

    <x-filament::modal slide-over id="my-cart">
        @foreach ($cart as $item)
            {{ $item->name }}
        @endforeach
    </x-filament::modal>

    <x-filament::modal slide-over id="product-detail">
        <div class="relative">
            <button type="button" class="fixed p-1 rounded-full top-5 left-5 bg-gray-100/50 md:hidden" wire:click="$dispatch('close-modal', { id: 'product-detail' })">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            <button type="button" class="fixed p-1 rounded-full top-5 right-5 bg-gray-100/50" wire:click="$dispatch('close-modal', { id: 'product-detail' })">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </button>

            <img src="{{ $showed?->getFirstMediaUrl('banner') }}" alt="{{ $showed?->description }}" class="object-cover object-center w-full h-64">
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
                    :icon="$this->cart->contains($showed) ? 'heroicon-m-check' : 'heroicon-m-shopping-bag'"
                    wire:click="$dispatch('add-to-cart', { product: '{{ $showed?->id }}' })"
                >
                    Add to cart
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>
</div>
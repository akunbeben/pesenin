<x-filament::modal slide-over id="product-detail">
    <div class="relative" x-data="{ f: null }">
        <button type="button" class="absolute z-10 p-1 rounded-full top-5 left-5 bg-gray-100/50 md:hidden" x-on:click="$dispatch('close-modal', { id: 'product-detail' })">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </button>

        @if (false)
        <button type="button" class="absolute z-10 p-1 rounded-full top-5 right-5 bg-gray-100/50" x-on:click="$dispatch('close-modal', { id: 'product-detail' })">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
        </button>
        @endif

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
        <span class="text-lg font-semibold text-gray-950 dark:text-white">{{ $showed?->name }}</span>
        <p class="text-sm font-normal text-gray-950 dark:text-white line-clamp-5">{{ $showed?->description }}</p>
        <div class="grid gap-2.5 {{ 'grid-cols-' . count($showed?->variants ?? []) }}">
            @forelse ($showed?->variants ?? [] as $variant)
            <button
                class="relative p-2.5 text-sm flex gap-2.5 justify-center border {{ $this->variant === $variant ? 'border-primary-500 text-primary-500' : 'border-gray-500 text-gray-950 dark:text-white' }} rounded-xl"
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

        @features('feature_payment', $this->table->merchant)
        <div class="flex w-full mt-auto bottom-5">
            <x-filament::button
                class="w-full"
                size="xl"
                wire:click="$dispatch('add-to-cart', { product: '{{ $showed?->id }}' })"
            >
                {{ __('Add to my order') }}
            </x-filament::button>
        </div>
        @endfeatures
    </div>
</x-filament::modal>
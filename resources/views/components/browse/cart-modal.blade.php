<x-filament::modal slide-over id="my-cart" class="min-h-screen">
    <div class="flex items-center justify-between p-2.5 md:hidden">
        <button type="button" class="p-1 rounded-full top-5 left-5 bg-gray-100/50" x-on:click="$dispatch('close-modal', { id: 'my-cart' })">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </button>

        @if (false)
        <button type="button" class="z-10 p-1 rounded-full top-5 right-5 bg-gray-100/50" x-on:click="$dispatch('close-modal', { id: 'my-cart' })">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
            </svg>
        </button>
        @endif
    </div>

    <div class="flex flex-col p-2 grow gap-1.5">
        <div class="max-h-[calc(90vh_-_145px)] overflow-auto flex flex-col gap-1.5">
            @forelse ($cart as $item)
                <div class="grid grid-cols-3 gap-2.5 border border-primary-500 dark:border-gray-700 p-2 rounded-xl" x-data="{ show: false }">
                    <img src="{{ $item['snapshot']['image'] }}" alt="{{ $item['snapshot']['name'] }}" class="object-cover object-center rounded-lg aspect-square">
                    <div class="flex flex-col col-span-2 gap-2.5 grow">
                        <span class="flex gap-1 text-gray-950 dark:text-white">
                            {{ $item['snapshot']['name'] }}
                            @if ($item['variant'])
                            <x-filament::badge class="w-fit">
                                {{ $item['variant'] }}
                            </x-filament::badge>
                            @endif
                        </span>

                        <span class="text-gray-950 dark:text-white">
                            {{ Number::currency($item['price'] * $item['amount'], 'IDR', config('app.locale')) }}
                        </span>

                        <div class="flex items-center justify-between mt-auto">
                            <x-filament::button
                                class="aspect-square"
                                size="xs"
                                outlined
                                wire:click="$dispatch('decrease-item', {product: {{ $item['product_id'] }}})"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                                </svg>
                            </x-filament::button>

                            <span class="text-gray-950 dark:text-white">{{ $item['amount'] }}</span>

                            <div class="flex gap-1.5">
                                <x-filament::button
                                    class="aspect-square"
                                    size="xs"
                                    outlined
                                    wire:click="$dispatch('increase-item', {product: {{ $item['product_id'] }}})"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                    </svg>
                                </x-filament::button>

                                <x-filament::button
                                    class="aspect-square"
                                    size="xs"
                                    outlined
                                    x-on:click="show = !show"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32l8.4-8.4z" />
                                        <path d="M5.25 5.25a3 3 0 00-3 3v10.5a3 3 0 003 3h10.5a3 3 0 003-3V13.5a.75.75 0 00-1.5 0v5.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V8.25a1.5 1.5 0 011.5-1.5h5.25a.75.75 0 000-1.5H5.25z" />
                                    </svg>
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                    <x-filament::input.wrapper suffix-icon="heroicon-m-pencil-square" class="col-span-3" x-show="show">
                        <x-filament::input
                            tabindex="-1"
                            autocomplete="off"
                            type="text"
                            class="!w-1/2 py-3.5"
                            placeholder="{{ __('Order note') }}"
                            wire:model.blur="cart.{{ $loop->index }}.note"
                        />
                    </x-filament::input.wrapper>
                </div>
            @empty
            <div class="flex flex-col items-center justify-center w-full h-full col-span-2 row-span-2 gap-5 text-gray-500 md:col-span-4">
                <div class="p-5 bg-gray-300 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <span class="px-10 text-base font-semibold text-center">{{ __('Your order is currently empty.') }}</span>
            </div>
            @endforelse
        </div>

        @if ($this->cart->isNotEmpty())
            @php
                $subTotal = $this->cart->sum(fn ($cartItem) => $cartItem['price'] * $cartItem['amount']);
                $tax = 0;
                $fee = 0;
            @endphp
            @features('feature_payment', $this->table->merchant)
            <div class="flex flex-col gap-2.5 w-full mt-auto bottom-5">
                <div class="flex flex-col gap-1.5">
                    @if ($this->paymentMethod)
                        @features('feature_tax', $this->table->merchant)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-950 dark:text-white">PPN 11%</span>
                            <span class="text-gray-950 dark:text-white">
                                {{ Number::currency($tax = $subTotal * 0.11, 'IDR', config('app.locale')) }}
                            </span>
                        </div>
                        @endfeatures
                        @features('feature_fee', $this->table->merchant)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-950 dark:text-white">{{ __('Admin fee 4%') }}</span>
                            <span class="text-gray-950 dark:text-white">
                                {{
                                    Number::currency($fee = $subTotal * match ($this->paymentMethod) {
                                        'ewallet' => $this->feeEwallet,
                                        default => $this->feeQRIS,
                                    }, 'IDR', config('app.locale'))
                                }}
                            </span>
                        </div>
                        @endfeatures
                    @endif
                    <hr class="w-full border-t border-gray-200 border-dashed dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-950 dark:text-white">{{ __('Total') }}</span>
                        <span class="text-gray-950 dark:text-white">
                            {{ Number::currency($subTotal + $tax + $fee, 'IDR', config('app.locale')) }}
                        </span>
                    </div>
                </div>
                @if ($this->paymentMethod)
                <x-filament::button
                    class="w-full"
                    size="xl"
                    wire:loading.attr="disabled"
                    wire:target="$dispatch('pay-now')"
                    wire:click="$dispatch('pay-now')"
                >
                    {{ __('Pay now') }}
                </x-filament::button>
                @else
                <x-filament::button
                    class="w-full"
                    size="xl"
                    wire:loading.attr="disabled"
                    wire:target="$dispatch('choose-payment')"
                    wire:click="$dispatch('choose-payment')"
                >
                    {{ __('Choose payment method') }}
                </x-filament::button>
                @endif
            </div>
            @endfeatures
        @else
        <div class="flex w-full mt-auto bottom-5">
            <x-filament::button
                class="w-full"
                size="xl"
                x-on:click="$dispatch('close-modal', { id: 'my-cart' })"
            >
                {{ __('Explore more products') }}
            </x-filament::button>
        </div>
        @endif
    </div>
</x-filament::modal>
@features('feature_payment', $this->table->merchant)
<div class="fixed bottom-0 left-0 right-0 z-40 flex items-center justify-center w-full px-2 mx-auto sm:max-w-sm md:max-w-3xl max-h-fit">
    <x-filament::button
        x-on:click="$dispatch('open-modal', { id: 'my-cart' })"
        size="xl"
        class="w-full my-2.5"
        badgeColor="danger"
        x-show="!scrolling && !!cart.length"
        style="display: none;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-90"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-90"
    >
        {{ __('View my order') }}

        <x-slot name="badge">
            <span x-text="cart.reduce((n, i) => n + i.amount, 0)"></span>
        </x-slot>
    </x-filament::button>
</div>
@endfeatures
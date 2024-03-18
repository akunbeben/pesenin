@php
    use App\Traits\Orders\PaymentChannels;
@endphp

<x-filament::modal id="payment-method" width="md">
    <x-slot name="heading">
        {{ __('Choose payment method') }}
    </x-slot>

    <div class="p-6">
        <div class="flex flex-col text-gray-950 dark:text-white gap-2.5 text-center">
            @foreach (PaymentChannels::cases() as $payment)
                @if ($scan->table->merchant->setting->{$payment->id()})
                <button
                    x-bind:class="{
                        'border rounded-xl p-2.5 flex flex-col gap-2.5 justify-center items-center relative': true,
                        'border-gray-300 dark:border-gray-800': '{{ $payment->id() }}' !== paymentMethod,
                        'border-primary-500': '{{ $payment->id() }}' === paymentMethod,
                    }"
                    type="button"
                    x-on:click="paymentMethod = '{{ $payment->id() }}'"
                >
                    <span class="absolute flex w-2 h-2 rounded-full top-2 left-2 bg-sky-500" x-show="paymentMethod === '{{ $payment->id() }}'"></span>
                    <img src="{{ $payment->getLogo() }}" alt="{{ $payment->getLabel() }}" class="mx-auto max-h-16">
                    <span>{{ $payment->getLabel() }}</span>
                    <span class="text-sm">{{ $payment->getDescription() }}</span>
                </button>
                @endif
            @endforeach

            <x-filament::button
                class="w-full"
                size="xl"
                x-show="!!paymentMethod"
                x-on:click="$dispatch('close-modal', { id: 'payment-method' })"
            >
                {{ __('Save payment method') }}
            </x-filament::button>
        </div>
    </div>
</x-filament::modal>
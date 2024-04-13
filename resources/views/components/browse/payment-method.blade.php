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
                    @class([
                        'border rounded-xl p-2.5 flex flex-col gap-2.5 justify-center items-center relative',
                        'border-gray-300 dark:border-gray-800' => $payment->id() !== $this->paymentMethod,
                        'border-primary-500' => $payment->id() === $this->paymentMethod,
                    ])
                    type="button"
                    wire:click="$set('paymentMethod', '{{ $payment->id() }}')"
                >
                    @if ($payment->id() === $this->paymentMethod)
                    <span class="absolute flex w-2 h-2 rounded-full top-2 left-2 bg-sky-500"></span>
                    @endif
                    <img src="{{ $payment->getLogo() }}" alt="{{ $payment->getLabel() }}" class="mx-auto max-h-16">
                    <span>{{ $payment->getLabel() }}</span>
                    <span class="text-sm">{{ $payment->getDescription() }}</span>
                </button>
                @endif
            @endforeach

            @if ($this->paymentMethod)
            <x-filament::button
                class="w-full"
                size="xl"
                x-on:click="$dispatch('close-modal', { id: 'payment-method' })"
            >
                {{ __('Save payment method') }}
            </x-filament::button>
            @endif
        </div>
    </div>
</x-filament::modal>
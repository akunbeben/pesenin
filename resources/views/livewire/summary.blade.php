@php
    use \Illuminate\Support\Number;
@endphp

<div class="h-screen max-h-screen p-2 mx-auto overflow-y-auto subpixel-antialiased sm:max-w-sm md:max-w-3xl" wire:poll.5s>
    <div class="flex items-center w-full h-full">
        <div class="flex flex-col items-center w-full gap-5 p-5 bg-gray-100 border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-xl">
            <div
                @class([
                    'flex items-center justify-center w-20 h-20 p-5 rounded-full',
                    'text-warning-500 bg-warning-100' => $this->order->status === \App\Traits\Orders\Status::Pending,
                    'text-primary-500 bg-primary-100' => $this->order->status === \App\Traits\Orders\Status::Processed,
                    'text-success-500 bg-success-100' => $this->order->status === \App\Traits\Orders\Status::Success,
                ])
            >
                <x-filament::icon
                    icon="{{ $this->order->status->icon() }}"
                    label="Payment status icon"
                    class="payment-icon"
                />
            </div>

            <div class="flex flex-col gap-1.5 items-center">
                <h1 class="text-base sm:text-3xl text-gray-950 dark:text-white">{{ __('Payment :status!', ['status' => __($this->order->status->name)]) }}</h1>
                @if ($this->order->status !== \App\Traits\Orders\Status::Success)
                <small class="text-gray-800 dark:text-gray-400">{{ __('Please wait until the payment success.') }}</small>
                @endif
                <span class="text-lg font-bold sm:text-3xl text-gray-950 dark:text-white">{{ Number::currency($this->order->total, 'IDR', 'id') }}</span>
            </div>

            <hr class="w-full border-t border-gray-200 dark:border-gray-700">

            <div class="flex flex-col gap-1.5 w-full text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-950 dark:text-white">{{ __('Ref') }}</span>
                    <span class="text-gray-950 dark:text-white">{{ $this->order->number }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-950 dark:text-white">{{ __('Payment time') }}</span>
                    <span class="text-gray-950 dark:text-white">{{ $this->order->created_at->isoFormat('dddd, D MMM Y HH:mm') }}</span>
                </div>

                <div class="flex my-5 flex-col gap-1.5">
                    <span class="text-gray-950 dark:text-white">{{ __('Ordered items') }}</span>
                    <hr class="w-full border-t border-gray-200 border-dashed dark:border-gray-700">
                </div>

                @foreach ($this->order->items as $item)
                <div class="flex items-center justify-between">
                    <span class="text-gray-950 dark:text-white">{{ $item->snapshot->name }}</span>
                    <span class="text-gray-950 dark:text-white">
                        {{ $item->amount }}x {{ Number::currency($item->snapshot->price, 'IDR', config('app.locale')) }}
                    </span>
                </div>
                @endforeach

                @if($this->order->additional?->tax)
                <div class="flex items-center justify-between">
                    <span class="text-gray-950 dark:text-white">PPN 11%</span>
                    <span class="text-gray-950 dark:text-white">
                        {{ Number::currency($this->order->additional->tax, 'IDR', config('app.locale')) }}
                    </span>
                </div>
                @endif

                @if($this->order->additional?->tax)
                <div class="flex items-center justify-between">
                    <span class="text-gray-950 dark:text-white">{{ __('Admin fee 4%') }}</span>
                    <span class="text-gray-950 dark:text-white">
                        {{ Number::currency($this->order->additional->fee, 'IDR', config('app.locale')) }}
                    </span>
                </div>
                @endif

                <div class="flex items-center justify-between font-semibold">
                    <span class="text-gray-950 dark:text-white">{{ __('Total') }}</span>
                    <span class="text-gray-950 dark:text-white">{{ Number::currency($this->order->total, 'IDR', 'id') }}</span>
                </div>

                <div class="flex no-print">
                    @if ($this->order->status === \App\Traits\Orders\Status::Success)
                    <x-filament::button class="w-full mt-10" size="xl" x-on:click="() => window.print()">
                        {{ __('Download receipt') }}
                    </x-filament::button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@php
    use \Illuminate\Support\Number;
    use \App\Traits\Orders\Status;
@endphp

<div
    class="h-screen max-w-sm max-h-screen p-2 mx-auto overflow-y-auto subpixel-antialiased"
    @if (!in_array($this->order->status, [Status::Expired, Status::Success]))
    wire:poll.5s
    @endif
>
    <div class="flex items-center w-full h-full">
        <div class="flex flex-col items-center w-full gap-5 p-5 bg-gray-100 border border-gray-200 dark:bg-gray-900 dark:border-gray-800 rounded-xl">
            <div
                @class([
                    'flex items-center justify-center',
                    'rounded-full w-20 h-20 p-5' => $this->order->status !== Status::Manual,
                    'text-warning-500 bg-warning-100' => $this->order->status === Status::Pending,
                    'text-primary-500 bg-primary-100' => $this->order->status === Status::Processed,
                    'text-success-500 bg-success-100' => $this->order->status === Status::Success,
                    'text-danger-500 bg-danger-100' => $this->order->status === Status::Expired,
                ])
            >
                @if ($this->order->status === Status::Manual)
                <div class="p-2.5 bg-white rounded-lg">{!! QrCode::size(150)->generate($this->order->number) !!}</div>
                @else
                <x-filament::icon
                    icon="{{ $this->order->status->icon() }}"
                    label="Payment status icon"
                    class="payment-icon"
                    @class([
                        'payment-icon',
                        'animate-spin' => $this->order->status === Status::Processed,
                    ])
                />
                @endif
            </div>

            <div class="flex flex-col gap-1.5 items-center text-center">
                <h1 class="text-base sm:text-2xl text-gray-950 dark:text-white">{{ __('Payment :status!', ['status' => __($this->order->status->name)]) }}</h1>
                @if ($this->order->status === Status::Manual)
                <small class="text-gray-800 dark:text-gray-400">{{ __('Ask merchant\'s employee to scan this QRCode to confirm your payment.') }}</small>
                @elseif ($this->order->status !== Status::Success)
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

                @if ($this->order->status !== Status::Manual)
                <div class="flex items-center justify-between">
                    <span class="text-gray-950 dark:text-white">{{ __('Payment time') }}</span>
                    <span class="text-gray-950 dark:text-white">{{ $this->order->updated_at->isoFormat('dddd, D MMM Y HH:mm') }}</span>
                </div>
                @endif

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

                @if($tax = $this->order->additional->where('type', 'tax')->first())
                <div class="flex items-center justify-between">
                    <span class="text-gray-950 dark:text-white">PPN 11%</span>
                    <span class="text-gray-950 dark:text-white">
                        {{ Number::currency($tax['value'], 'IDR', config('app.locale')) }}
                    </span>
                </div>
                @endif

                @if($fee = $this->order->additional->where('type', 'fee')->first())
                @php
                    $percent = Number::format($fee['value'] / ($this->order->total - $tax['value']) * 100, precision: 1);
                @endphp
                <div class="flex items-center justify-between">
                    <span class="text-gray-950 dark:text-white">{{ __('Payment gateway fee :percent%', ['percent' => $percent]) }}</span>
                    <span class="text-gray-950 dark:text-white">
                        {{ Number::currency($fee['value'], 'IDR', config('app.locale')) }}
                    </span>
                </div>
                @endif

                <div class="flex items-center justify-between font-semibold">
                    <span class="text-gray-950 dark:text-white">{{ __('Total') }}</span>
                    <span class="text-gray-950 dark:text-white">{{ Number::currency($this->order->total, 'IDR', 'id') }}</span>
                </div>

                <div class="flex no-print">
                    @if ($this->order->status === Status::Success)
                    <x-filament::button class="w-full mt-10" size="xl" x-on:click="() => window.print()">
                        {{ __('Download receipt') }}
                    </x-filament::button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

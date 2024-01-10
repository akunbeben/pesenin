@php
    use Filament\Facades\Filament;
    use App\Traits\Orders\Serving;
    use Laravel\Pennant\Feature;
@endphp

<x-filament-panels::page>
    <div class="grid gap-5 sm:grid-cols-3" wire:poll="loads">
        <div class="flex flex-col gap-5" x-data="{ timeout: 100 }">
            <span class="text-2xl font-bold tracking-tight text-center sm:text-3xl text-warning-400">
                {{ __('Waiting') }}
            </span>

            @foreach ($this->waiting as $order)
            <div
                x-data="{ shown: false }"
                x-init="setTimeout(() => { shown = true }, timeout * {{ $loop->iteration * 2.5 }})"
            >
                <div
                    x-show="shown"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="relative p-6 bg-white shadow-sm fi-wi-stats-overview-stat rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 h-fit"
                >
                    <div class="grid gap-y-5">
                        <div
                            @class([
                                'font-semibold tracking-tight text-gray-950 dark:text-white',
                                'text-3xl' => !Feature::for(Filament::getTenant())->active('feature_ikiosk'),
                                'text-xl' => Feature::for(Filament::getTenant())->active('feature_ikiosk'),
                            ])
                        >
                            @features('feature_ikiosk', Filament::getTenant())
                            {{ $order->number }}
                            @else
                            {{ __('Table') }} &mdash; {{ $order->scan->table->name }}
                            @endfeatures
                        </div>

                        <div class="flex flex-col gap-2">
                            @foreach ($order->items as $item)
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $item->amount }}x {{ $item->snapshot->name }}
                            </span>
                            @endforeach
                        </div>

                        <div class="flex flex-col items-center justify-between lg:flex-row">
                            <div></div>
                            <x-filament::button
                                icon="heroicon-m-arrow-right"
                                icon-position="after"
                                wire:click="$dispatch('forward', { order: '{{ $order->getRouteKey() }}' })"
                                label="Continue"
                                class="w-full"
                            >
                                {{ __('Continue') }}
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="flex flex-col gap-5" x-data="{ timeout: 100 }">
            <span class="text-2xl font-bold tracking-tight text-center sm:text-3xl text-primary-500">
                {{ __('Processed') }}
            </span>

            @foreach ($this->processed as $order)
            <div
                x-data="{ shown: false }"
                x-init="setTimeout(() => { shown = true }, timeout * {{ $loop->iteration * 2.5 }})"
            >
                <div
                    x-show="shown"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="relative p-6 bg-white shadow-sm fi-wi-stats-overview-stat rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 h-fit"
                >
                    <div class="grid gap-y-5">
                        <div
                            @class([
                                'font-semibold tracking-tight text-gray-950 dark:text-white',
                                'text-3xl' => !Feature::for(Filament::getTenant())->active('feature_ikiosk'),
                                'text-xl' => Feature::for(Filament::getTenant())->active('feature_ikiosk'),
                            ])
                        >
                            @features('feature_ikiosk', Filament::getTenant())
                            {{ $order->number }}
                            @else
                            {{ __('Table') }} &mdash; {{ $order->scan->table->name }}
                            @endfeatures
                        </div>

                        <div class="flex flex-col gap-2">
                            @foreach ($order->items as $item)
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $item->amount }}x {{ $item->snapshot->name }}
                            </span>
                            @endforeach
                        </div>

                        <div class="flex flex-col items-center justify-between lg:flex-row">
                            <x-filament::button
                                color="warning"
                                icon="heroicon-m-arrow-left"
                                icon-position="before"
                                wire:click="$dispatch('backward', { order: '{{ $order->getRouteKey() }}' })"
                                label="Go back"
                                class="w-full"
                            >
                                {{ __('Go back') }}
                            </x-filament::button>

                            <x-filament::button
                                icon="heroicon-m-arrow-right"
                                icon-position="after"
                                wire:click="$dispatch('forward', { order: '{{ $order->getRouteKey() }}' })"
                                label="Continue"
                                class="w-full"
                            >
                                {{ __('Continue') }}
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="flex flex-col gap-5" x-data="{ timeout: 100 }">
            <span class="text-2xl font-bold tracking-tight text-center sm:text-3xl text-success-500">
                {{ __('Completed') }}
            </span>

            @foreach ($this->completed as $order)
            <div
                x-data="{ shown: false }"
                x-init="setTimeout(() => { shown = true }, timeout * {{ $loop->iteration * 2.5 }})"
            >
                <div
                    x-show="shown"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="relative p-6 bg-white shadow-sm fi-wi-stats-overview-stat rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 h-fit"
                >
                    <div class="grid gap-y-5">
                        <div
                            @class([
                                'font-semibold tracking-tight text-gray-950 dark:text-white',
                                'text-3xl' => !Feature::for(Filament::getTenant())->active('feature_ikiosk'),
                                'text-xl' => Feature::for(Filament::getTenant())->active('feature_ikiosk'),
                            ])
                        >
                            @features('feature_ikiosk', Filament::getTenant())
                            {{ $order->number }}
                            @else
                            {{ __('Table') }} &mdash; {{ $order->scan->table->name }}
                            @endfeatures
                        </div>

                        <div class="flex flex-col gap-2">
                            @foreach ($order->items as $item)
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $item->amount }}x {{ $item->snapshot->name }}
                            </span>
                            @endforeach
                        </div>

                        <div class="flex flex-col items-center justify-between lg:flex-row">
                            <x-filament::button
                                color="warning"
                                icon="heroicon-m-arrow-left"
                                icon-position="before"
                                wire:click="$dispatch('backward', { order: '{{ $order->getRouteKey() }}' })"
                                label="Go back"
                                class="w-full"
                            >
                                {{ __('Go back') }}
                            </x-filament::button>

                            <x-filament::button
                                color="success"
                                icon="heroicon-m-check"
                                icon-position="after"
                                wire:click="$dispatch('forward', { order: '{{ $order->getRouteKey() }}' })"
                                label="Finish"
                                class="w-full"
                            >
                                {{ __('Finish') }}
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>

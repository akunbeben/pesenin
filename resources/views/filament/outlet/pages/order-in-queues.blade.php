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

            @foreach ($this->waiting as $waiting)
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
                        <div class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">
                            @features('feature_ikiosk', Filament::getTenant())
                            {{ $waiting->number }}
                            @else
                            {{ __('Table') }} &mdash; {{ $waiting->loadMissing(['items', 'scan.table'])->scan->table->name }}
                            @endfeatures
                        </div>

                        <div class="flex flex-col gap-2">
                            <span class="text-xs font-medium text-gray-500 lg:text-sm dark:text-gray-400">{{ $waiting->number }}</span>
                            @foreach ($waiting->items as $item)
                            <span class="text-xs font-medium lg:text-sm dark:text-gray-400">
                                {{ $item->amount }}x {{ $item->snapshot->name }}
                            </span>
                            @endforeach
                        </div>

                        <div class="flex flex-col items-center justify-between lg:flex-row">
                            <div></div>
                            <x-filament::button
                                icon="heroicon-m-arrow-right"
                                icon-position="after"
                                wire:click="$dispatch('forward', { order: '{{ $waiting->getRouteKey() }}' })"
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

            @foreach ($this->processed as $processed)
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
                        <div class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">
                            @features('feature_ikiosk', Filament::getTenant())
                            <span>{{ $processed->number }}</span>
                            @else
                            <span>{{ __('Table') }} &mdash; {{ $processed->loadMissing(['items', 'scan.table'])->scan->table->name }}</span>
                            @endfeatures
                        </div>

                        <div class="flex flex-col gap-2">
                            <span class="text-xs font-medium text-gray-500 lg:text-sm dark:text-gray-400">{{ $processed->number }}</span>
                            @foreach ($processed->items as $item)
                            <span class="text-xs font-medium lg:text-sm dark:text-gray-400">
                                {{ $item->amount }}x {{ $item->snapshot->name }}
                            </span>
                            @endforeach
                        </div>

                        <div class="flex flex-col items-center justify-between gap-2 lg:flex-row">
                            <x-filament::button
                                color="warning"
                                icon="heroicon-m-arrow-left"
                                icon-position="before"
                                wire:click="$dispatch('backward', { order: '{{ $processed->getRouteKey() }}' })"
                                label="Go back"
                                class="w-full"
                            >
                                {{ __('Go back') }}
                            </x-filament::button>

                            <x-filament::button
                                icon="heroicon-m-arrow-right"
                                icon-position="after"
                                wire:click="$dispatch('forward', { order: '{{ $processed->getRouteKey() }}' })"
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

            @foreach ($this->completed as $completed)
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
                        <div class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">
                            @features('feature_ikiosk', Filament::getTenant())
                            {{ $completed->number }}
                            @else
                            {{ __('Table') }} &mdash; {{ $completed->loadMissing(['items', 'scan.table'])->scan->table->name }}
                            @endfeatures
                        </div>

                        <div class="flex flex-col gap-2">
                            <span class="text-xs font-medium text-gray-500 lg:text-sm dark:text-gray-400">{{ $completed->number }}</span>
                            @foreach ($completed->items as $item)
                            <span class="text-xs font-medium lg:text-sm dark:text-gray-400">
                                {{ $item->amount }}x {{ $item->snapshot->name }}
                            </span>
                            @endforeach
                        </div>

                        <div class="flex flex-col items-center justify-between gap-2 lg:flex-row">
                            <x-filament::button
                                color="warning"
                                icon="heroicon-m-arrow-left"
                                icon-position="before"
                                wire:click="$dispatch('backward', { order: '{{ $completed->getRouteKey() }}' })"
                                label="Go back"
                                class="w-full"
                            >
                                {{ __('Go back') }}
                            </x-filament::button>

                            <x-filament::button
                                color="success"
                                icon="heroicon-m-check"
                                icon-position="after"
                                wire:click="$dispatch('forward', { order: '{{ $completed->getRouteKey() }}' })"
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

@push('styles')
@vite('resources/js/app.js')
@endpush
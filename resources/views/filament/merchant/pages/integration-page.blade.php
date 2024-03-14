@php
    use Filament\Facades\Filament;
    use Illuminate\Support\Facades\Crypt;
@endphp

<x-filament-panels::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 2xl:grid-cols-4 gap-2.5">
        @if (!Filament::getTenant()->integration)
            @foreach ($this->services as $id => $party)
            <div class="flex flex-col items-center justify-center gap-10 p-5 bg-white shadow-sm sm:p-10 rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <img src="{{ asset("images/3rd-party/{$id}.png") }}" alt="Integrate to Pawoon" class="h-8">
                {{ $this->{$id} }}
            </div>
            @endforeach
            <div class="flex flex-col items-center justify-center gap-10 p-5 text-center bg-white shadow-sm sm:p-10 rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <span class="text-sm text-gray-500 sm:text-base fi-color-gray dark:text-gray-400">{{ __('More integration with other POS system is coming soon') }}</span>
            </div>
        @else
        <div class="flex flex-col items-center justify-center gap-10 p-5 bg-white shadow-sm sm:p-10 rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 col-span-full">
            <img src="{{ asset(sprintf('images/3rd-party/%s.png', Filament::getTenant()->integration->provider)) }}" alt="Integrated to {{ Filament::getTenant()->integration->provider }}" class="h-8">
            <div class="flex items-center gap-2.5">
                @if (Filament::getTenant()->external_id)
                <span class="text-sm text-gray-500 sm:text-base fi-color-gray dark:text-gray-400">
                    Integrated to {{ Filament::getTenant()->integration->provider }}
                </span>
                @else
                <span class="text-sm text-gray-500 sm:text-base fi-color-gray dark:text-gray-400">
                    Integration to {{ Filament::getTenant()->integration->provider }} is incomplete, please select the outlet.
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <x-filament::modal id="select-outlet" width="lg">
        <x-slot name="heading">
            {{ __('Select Outlet') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Select one of the outlet from your POS') }}
        </x-slot>

        <x-filament::input.wrapper>
            <x-filament::input.select wire:model="selectedOutlet" required>
                <option value="">{{ __('Select the outlet') }}</option>
                @foreach ($this->outlets ?? [] as $outlet)
                    <option value="{{ Crypt::encrypt(['id' => $outlet->id, 'name' => $outlet->name]) }}">{{ $outlet->name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-slot name="footerActions">
            <x-filament::button wire:click="$dispatch('save-outlet')">
                {{ __('Submit') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>

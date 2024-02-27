<x-filament-widgets::widget>
    <x-filament::section
        collapsible
    >
        <x-slot name="heading">
            {{ __('QR Code') }}
        </x-slot>

        <x-slot name="description">
            {{ __('QR Code for your customers') }}
        </x-slot>

        <div class="flex flex-col gap-2.5">
            <img src="{{ $this->table->getFirstMediaUrl('qr') }}" alt="Shareable QR Code" class="rounded-lg">
            <div class="flex gap-2.5">
                {{ $this->visitAction }}
                {{ $this->downloadAction }}
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

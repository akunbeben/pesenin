<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('QR Code') }}
        </x-slot>

        <x-slot name="description">
            {{ __('QR Code for your customers') }}
        </x-slot>

        <img src="{{ $this->table->getFirstMediaUrl('qr') }}" alt="Shareable QR Code" class="rounded-lg">
    </x-filament::section>
</x-filament-widgets::widget>

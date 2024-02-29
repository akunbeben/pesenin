<x-filament-widgets::widget>
    <x-filament::section wire:poll.5s="checkProgress()">
        <div class="flex flex-col items-center justify-center h-20 text-center">
            <div class="flex items-center gap-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 animate-spin">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>We are preparing your dashboard. We will refresh the page as it completed. Please wait ...</span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

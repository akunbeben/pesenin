<div
    class="max-h-screen px-2 mx-auto overflow-y-auto subpixel-antialiased sm:max-w-sm md:max-w-3xl"
    style="scrollbar-gutter: stable;"
>
    <div id="grid" class="grid grid-cols-2 md:grid-cols-4 gap-y-3.5 gap-x-2.5 py-2">
        @forelse ($tables as $table)
        <div class="relative flex flex-col items-center justify-center p-10 border gap-y-2 rounded-xl">
            <div wire:click="$dispatch('choose-table', { table: {{ $table->getKey() }} })" class="relative flex flex-col cursor-pointer gap-y-2">
                <div class="flex items-center gap-1">
                    <span class="text-xl font-semibold line-clamp-1 text-gray-950 dark:text-white">{{ $table->name }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center w-full col-span-2 row-span-2 gap-5 text-gray-500 md:col-span-4 h-80">
            <div class="p-5 bg-gray-300 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <span class="px-10 text-base font-semibold text-center">{{ __('We couldn\'t find any tables matching your search criteria.') }}</span>
        </div>
        @endforelse
    </div>
</div>

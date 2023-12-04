<div x-data="{
    isDark: null,
    init: function () {
        this.isDark = document.documentElement.classList.contains('dark')
    },
}">
    <img @theme-changed.window="this.isDark = document.documentElement.classList.contains('dark')" x-show="!isDark" src="{{ asset('logo.png') }}" alt="{{ config('app.name') }} Logo" class="h-full max-w-[250px] text-gray-500 fill-current" style="max-width: 250px;" />
    <img @theme-changed.window="this.isDark = document.documentElement.classList.contains('dark')" x-show="isDark" src="{{ asset('logo-dark.png') }}" alt="{{ config('app.name') }} Logo" class="h-full max-w-[250px] text-gray-500 fill-current" style="max-width: 250px;" />
</div>
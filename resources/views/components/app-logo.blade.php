@props(['class'=> null, 'url' => null])

<a
    href="{{ $url ?? route('home') }}"
    target="_blank"
    x-data="{
        isDark: false,
        init: function () {
            const theme = localStorage.getItem('theme') ?? @js(filament()->getDefaultThemeMode()->value)

            if (
                theme === 'dark' ||
                (theme === 'system' &&
                    window.matchMedia('(prefers-color-scheme: dark)')
                        .matches)
            ) {
                document.documentElement.classList.add('dark')
            }

            this.isDark = document.documentElement.classList.contains('dark')
        },
    }"
>
    <img
        @theme-changed.window="this.isDark = document.documentElement.classList.contains('dark')"
        x-show.important="!isDark"
        src="{{ asset('logo.png') }}"
        alt="{{ config('app.name') }} Logo"
        class="h-full max-w-[250px] text-gray-500 fill-current {{ $class }}"
        style="display: inline;"
    />
    <img
        @theme-changed.window="this.isDark = document.documentElement.classList.contains('dark')"
        x-show.important="isDark"
        src="{{ asset('logo-dark.png') }}"
        alt="{{ config('app.name') }} Logo"
        class="h-full max-w-[250px] text-gray-500 fill-current {{ $class }}"
        style="display: none;"
    />
</a>
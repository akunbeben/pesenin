<picture>
    <source srcset="{{ asset('logo.png') }}" media="(prefers-color-scheme: light)" />
    <source srcset="{{ asset('logo-dark.png') }}" media="(prefers-color-scheme: dark)" />
    <img src="{{ asset('logo.png') }}" alt="Laravel Logo" class="h-auto text-gray-500 fill-current w-60" />
</picture>
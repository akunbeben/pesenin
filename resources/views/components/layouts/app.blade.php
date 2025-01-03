<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('components.gtag')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:300,400,500,600,700" rel="stylesheet" />

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @filamentStyles
    @filamentScripts
    @livewireStyles
    @livewireScripts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('scripts')
</head>

<body
    @class([
        'font-sans antialiased bg-gray-50 dark:bg-gray-950',
        'dark' => filament()->hasDarkModeForced(),
    ])
>
    <div class="min-h-screen">
        <main>
            {{ $slot }}
        </main>
    </div>
    @livewire('notifications')
</body>

</html>
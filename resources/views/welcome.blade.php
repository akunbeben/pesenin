<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <meta property="og:title" content="pesenin.online - Scan, pilih, Bayar. Pesanan datang 🚀">
        <meta property="og:site_name" content="pesenin.online">
        <meta property="og:url" content="{{ config('app.url') }}">
        <meta property="og:description" content="Menu QR tuh harusnya gini! Scan QR pesenin, pilih menu favorit, langsung bayar, pesanan datang. Hemat waktu, tanpa repot, sebenar-benarnya solusi menu QR.">
        <meta property="og:type" content="website">
        <meta property="og:image" content="{{ asset('twitter-card.png') }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="pesenin.online - Scan, pilih, Bayar. Pesanan datang 🚀">
        <meta name="twitter:site" content="pesenin.online">
        <meta name="twitter:description" content="Menu QR tuh harusnya gini! Scan QR pesenin, pilih menu favorit, langsung bayar, pesanan datang. Hemat waktu, tanpa repot, sebenar-benarnya solusi menu QR.">
        <meta name="twitter:image" content="{{ asset('twitter-card.png') }}">
        <meta name="twitter:image:alt" content="pesenin.online - Scan, pilih, Bayar. Pesanan datang 🚀">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}" />
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}" />
        <link rel="manifest" href="{{ asset('site.webmanifest') }}" />

        <!-- Styles -->
        @filamentStyles
        @vite('resources/css/app.css')
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gray-100 bg-center sm:flex sm:justify-center sm:items-center bg-dots-darker dark:bg-dots-lighter dark:bg-gray-900 selection:bg-primary-500 selection:text-white">
            <div class="flex flex-col items-center justify-center h-screen p-6 mx-auto max-w-7xl lg:p-8">
                <div class="flex justify-center">
                    <a href="/">
                        <picture>
                            <source srcset="{{ asset('logo.png') }}" media="(prefers-color-scheme: light)" />
                            <source srcset="{{ asset('logo-dark.png') }}" media="(prefers-color-scheme: dark)" />
                            <img src="{{ asset('logo.png') }}" alt="Laravel Logo" class="h-auto text-gray-500 fill-current w-60" />
                        </picture>
                    </a>
                </div>

                <div class="mt-16">
                    <div class="flex items-center justify-center">
                        <a href="mailto:hello@pesenin.online" class="flex p-6 transition-all motion-safe:hover:scale-[1.01] scale-100 bg-white rounded-lg shadow-2xl dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 shadow-gray-500/20 dark:shadow-none duration-250 focus:outline focus:outline-2 focus:outline-primary-500">
                            <div class="flex flex-col items-center justify-center sm:items-start">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Coming Soon 🚀
                                </h2>

                                <p class="mt-4 text-sm leading-relaxed text-center text-gray-500 dark:text-gray-400 sm:text-left">
                                    Kami sedang menyiapkan sesuatu yang luar biasa untuk Anda! Tingkatkan pengalaman pengguna anda.
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @filamentScripts
        @vite('resources/js/app.js')
    </body>
</html>

import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: !!process.env.CODESPACES,
        https: true,
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/central/theme.css',
                'resources/css/filament/merchant/theme.css',
            ],
            refresh: [...refreshPaths, 'app/Filament/**', 'app/Providers/Filament/**', 'app/Livewire/**'],
        }),
    ],
});

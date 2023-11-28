import { defineConfig } from 'vite'
import fs from 'fs'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import { homedir } from 'os'
import { resolve } from 'path'

let host = 'pesenin.test'

export default defineConfig({
    // server: detectServerConfig(host),
    server: {
        hmr: '127.0.0.1',
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
})

function detectServerConfig(host) {
    let keyPath = resolve(homedir(), `.valet/Certificates/${host}.key`)
    let certificatePath = resolve(homedir(), `.valet/Certificates/${host}.crt`)

    if (!fs.existsSync(keyPath)) {
        return {}
    }

    if (!fs.existsSync(certificatePath)) {
        return {}
    }

    return {
        hmr: { host },
        host,
        https: {
            key: fs.readFileSync(keyPath),
            cert: fs.readFileSync(certificatePath),
        },
    }
}


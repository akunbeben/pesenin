import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Outlet/**/*.php',
        './resources/views/filament/outlet/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}

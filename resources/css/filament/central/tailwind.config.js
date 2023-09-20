import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Central/**/*.php',
        './resources/views/filament/central/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}

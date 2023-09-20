import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Merchant/**/*.php',
        './resources/views/filament/merchant/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}

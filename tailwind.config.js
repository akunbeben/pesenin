import preset from './vendor/filament/support/tailwind.config.preset'

const colors = require('tailwindcss/colors');
const defaultTheme = require('tailwindcss/defaultTheme');

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                'sans': ['Be Vietnam Pro', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
            },
            colors: {
                custom: colors.sky,
                danger: colors.rose,
                gray: colors.gray,
                info: colors.blue,
                primary: colors.sky,
                success: colors.emerald,
                warning: colors.orange,
            },
        },
    },
}

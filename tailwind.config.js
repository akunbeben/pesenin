import preset from './vendor/filament/support/tailwind.config.preset'

const colors = require('tailwindcss/colors');
const defaultTheme = require('tailwindcss/defaultTheme');

export default {
    darkMode: 'class',
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        height: theme => ({
            auto: 'auto',
            ...theme('spacing'),
            full: '100%',
            screen: ['100vh', '100dvh'],
        }),
        minHeight: theme => ({
            '0': '0',
            ...theme('spacing'),
            full: '100%',
            screen: ['100vh', '100dvh'],
        }),
        extend: {
            colors: {
                custom: colors.sky,
                danger: colors.rose,
                info: colors.blue,
                primary: colors.sky,
                success: colors.emerald,
                warning: colors.orange,
            },
            fontFamily: {
                'sans': ['Be Vietnam Pro', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
            },
        },
    },
}

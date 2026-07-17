import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand gradient drgEkspedisi: hijau (asal/pickup) -> biru (tujuan/delivered)
                brand: {
                    50: '#eefdf6',
                    100: '#d5f9e7',
                    200: '#adf1d1',
                    300: '#74e3b3',
                    400: '#3ecd93',
                    500: '#18b378', // hijau utama
                    600: '#0f9563',
                    700: '#107852',
                    800: '#125f43',
                    900: '#114e39',
                },
                transit: {
                    50: '#eef7ff',
                    100: '#d9ecff',
                    200: '#bcdfff',
                    300: '#8ecbff',
                    400: '#59acff',
                    500: '#3389fd', // biru utama
                    600: '#1c6cf2',
                    700: '#1857de',
                    800: '#1a47b3',
                    900: '#1b3e8c',
                },
            },
            backgroundImage: {
                'brand-gradient': 'linear-gradient(135deg, #18b378 0%, #3389fd 100%)',
                'brand-gradient-soft': 'linear-gradient(135deg, #eefdf6 0%, #eef7ff 100%)',
            },
        },
    },
    plugins: [],
};

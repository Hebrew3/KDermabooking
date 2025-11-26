import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Modern Pink Palette
                pink: {
                    50: '#fdf2f8',   // Very light pink
                    100: '#fce7f3',  // Light pink
                    200: '#fbcfe8',  // Soft pink
                    300: '#f9a8d4',  // Medium light pink
                    400: '#f472b6',  // Medium pink
                    500: '#ec4899',  // Primary pink
                    600: '#db2777',  // Medium dark pink
                    700: '#be185d',  // Dark pink
                    800: '#9d174d',  // Darker pink
                    900: '#831843',  // Very dark pink
                    950: '#500724',  // Darkest pink
                },
                // Rose variations for gradients
                rose: {
                    50: '#fff1f2',
                    100: '#ffe4e6',
                    200: '#fecdd3',
                    300: '#fda4af',
                    400: '#fb7185',
                    500: '#f43f5e',
                    600: '#e11d48',
                    700: '#be123c',
                    800: '#9f1239',
                    900: '#881337',
                    950: '#4c0519',
                },
                // Fuchsia for accent colors
                fuchsia: {
                    50: '#fdf4ff',
                    100: '#fae8ff',
                    200: '#f5d0fe',
                    300: '#f0abfc',
                    400: '#e879f9',
                    500: '#d946ef',
                    600: '#c026d3',
                    700: '#a21caf',
                    800: '#86198f',
                    900: '#701a75',
                    950: '#4a044e',
                },
                // Neutral grays with pink undertones
                neutral: {
                    50: '#fafafa',
                    100: '#f5f5f5',
                    200: '#e5e5e5',
                    300: '#d4d4d4',
                    400: '#a3a3a3',
                    500: '#737373',
                    600: '#525252',
                    700: '#404040',
                    800: '#262626',
                    900: '#171717',
                    950: '#0a0a0a',
                }
            },
            backgroundImage: {
                'gradient-pink': 'linear-gradient(135deg, #fdf2f8 0%, #fce7f3 50%, #fbcfe8 100%)',
                'gradient-pink-dark': 'linear-gradient(135deg, #831843 0%, #9d174d 50%, #be185d 100%)',
                'gradient-rose': 'linear-gradient(135deg, #fff1f2 0%, #ffe4e6 50%, #fecdd3 100%)',
                'gradient-fuchsia': 'linear-gradient(135deg, #fdf4ff 0%, #fae8ff 50%, #f5d0fe 100%)',
                'gradient-radial-pink': 'radial-gradient(circle, #fdf2f8 0%, #fce7f3 50%, #fbcfe8 100%)',
            },
            boxShadow: {
                'pink-sm': '0 1px 2px 0 rgba(236, 72, 153, 0.05)',
                'pink': '0 4px 6px -1px rgba(236, 72, 153, 0.1), 0 2px 4px -1px rgba(236, 72, 153, 0.06)',
                'pink-lg': '0 10px 15px -3px rgba(236, 72, 153, 0.1), 0 4px 6px -2px rgba(236, 72, 153, 0.05)',
                'pink-xl': '0 20px 25px -5px rgba(236, 72, 153, 0.1), 0 10px 10px -5px rgba(236, 72, 153, 0.04)',
                'pink-2xl': '0 25px 50px -12px rgba(236, 72, 153, 0.25)',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'bounce-gentle': 'bounceGentle 2s infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                bounceGentle: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
            },
        },
    },

    plugins: [forms],
};

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // BookCondition colors: green, blue, yellow
        // OrderStatus colors: yellow, blue, indigo, green, red, gray
        // BookStatus colors: yellow, green, red
        {
            pattern: /bg-(green|blue|yellow|red|gray|indigo)-(50|100|200)/,
            variants: [],
        },
        {
            pattern: /text-(green|blue|yellow|red|gray|indigo)-(700|800)/,
            variants: [],
        },
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};

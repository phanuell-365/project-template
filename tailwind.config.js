/** @type {import('tailwindcss').Config} */

module.exports = {
    content: [
        "./app/**/*.{html,js,php}",
        "./public/**/*.{html,js,php}",
        "./modules/**/*.{html,js,php}",
        "./node_modules/flowbite/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                'soko': {
                    '50': '#dff2f5',
                    '100': '#bfe5eb',
                    '200': '#9fd8e1',
                    '300': '#7ecbd7',
                    '400': '#5dbece',
                    '500': '#59baca',
                    '600': '#4fa9b8',
                    '700': '#4498a6',
                    '800': '#398794',
                    '900': '#2e7682',
                }
            },
            fontFamily: {
                'lato': ['Lato', 'Comic Neue', 'cursive', 'Ubuntu', 'sans-serif'],
                'poppins': ['Poppins', 'Comic Neue', 'cursive', 'Ubuntu', 'sans-serif'],
            },
            fontSize: {
                'xxs': '.65rem',
                'xs': '.75rem',
                'sm': '.875rem',
            },
            // add medium box shadow to different sides of an element
            boxShadow: {
                'md-left': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'md-right': '-0 4px 6px -1px rgba(0, 0, 0, 0.1), -0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                'md-top': '0 -4px 6px -1px rgba(0, 0, 0, 0.1), 0 -2px 4px -1px rgba(0, 0, 0, 0.06)',
                'md-bottom': '0 4px 4px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
            }
        },
    },
    daisyui: {
        themes: [
            {
                'soko': {
                    'primary': '#59baca',
                    "secondary": "#F000B8",
                    "accent": "#37CDBE",
                    "neutral": "#3D4451",
                    "base-100": "#FFFFFF",
                    "info": "#3ABFF8",
                    "success": "#36D399",
                    "warning": "#FBBD23",
                    "error": "#F87272",
                },
            },
        ]
    },
    plugins: [
        require('flowbite/plugin'),
        require("daisyui"),
        require('@tailwindcss/forms'),
    ],
}


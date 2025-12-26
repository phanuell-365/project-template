/** @type {import('tailwindcss').Config} */
module.exports = {
    // 1. Point ONLY to your email views
    content: [
        "./app/Views/emails/**/*.php",
        "./app/Services/TemplateService.php", // If you have HTML strings in services
    ],
    theme: {
        extend: {
            // Add custom email colors here if needed
            colors: {
                // 'soko': {
                //     '50': '#dff2f5',
                //     '100': '#bfe5eb',
                //     '200': '#9fd8e1',
                //     '300': '#7ecbd7',
                //     '400': '#5dbece',
                //     '500': '#59baca', // primary
                //     '600': '#4fa9b8',
                //     '700': '#4498a6',
                //     '800': '#398794',
                //     '900': '#2e7682',
                //     '950': '#16434d',
                // },
                'soko': {
                    '50': '#f6fee7',
                    '100': '#e9fdca',
                    '200': '#d4fa9c',
                    '300': '#b5f462',
                    '400': '#99e833',
                    '500': '#87e516',
                    '600': '#5ca50b',
                    '700': '#477d0e',
                    '800': '#3a6311',
                    '900': '#325413',
                    '950': '#182e05',
                },
                // 'soko': {
                //     '50': '#fff0f2',
                //     '100': '#ffdde0',
                //     '200': '#ffc0c6',
                //     '300': '#ff949f',
                //     '400': '#ff5768',
                //     '500': '#ff2339',
                //     '600': '#ff001a', // primary
                //     '700': '#d70016',
                //     '800': '#b10315',
                //     '900': '#920a18',
                //     '950': '#500008',
                // },
                // 'soko': {
                //     '50': '#fff1fe',
                //     '100': '#ffe1fe',
                //     '200': '#ffc3fe',
                //     '300': '#ff94f9',
                //     '400': '#ff54f6',
                //     '500': '#ff16f4',
                //     '600': '#f500ff',
                //     '700': '#d100d9',
                //     '800': '#ad00b1',
                //     '900': '#800080', // primary
                //     '950': '#620063',
                // },
            },
            fontFamily: {
                montserrat: ['Montserrat', 'Ubuntu', 'sans-serif'],
            },
            fontSize: {
                'xxxs': '.5rem',
                'xxs': '.65rem',
                'xs': '.75rem',
                'sm': '.875rem',
            },
        },
    },
    // 2. CRITICAL: Disable Preflight
    // This prevents Tailwind from adding the heavy "reset" styles
    // (like body { margin: 0 }, img { display: block }) that confuse the inliner.
    corePlugins: {
        preflight: false,
    },
    daisyui: {
        themes: [
            {
                'soko': {
                    'primary': '#87e516',
                    // 'primary': '#59baca',
                    // 'primary': '#ff001a',
                    // 'primary': '#800080',
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
        require("daisyui"),
    ],
}

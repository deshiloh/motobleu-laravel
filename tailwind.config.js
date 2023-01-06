const colors = require('tailwindcss/colors')

module.exports = {
    darkMode: ['class', '[data-theme="dark"]'],
    content: [
        "./app/Components/**/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
        './vendor/wireui/wireui/resources/**/*.blade.php',
        './vendor/wireui/wireui/ts/**/*.ts',
        './vendor/wireui/wireui/src/View/**/*.php'
    ],
    theme: {
        extend: {
            colors: {
                'motobleu' : {
                    light: '#222ca1',
                    DEFAULT: '#0A158D',
                    'dark' : '#081068'
                },
                'primary': {
                    '50': '#0A158D',
                    '100': '#0A158D',
                    '200': '#0A158D',
                    '300': '#0A158D',
                    '400': '#0A158D',
                    '500': '#0A158D',
                    '600': '#0A158D',
                    '700': '#0A158D',
                    '800': '#0A158D',
                    '900': '#0A158D'
                },
                'secondary': colors.gray,
                'positive': colors.emerald,
                'negative': colors.red,
                'warning': colors.amber,
                'info': colors.blue
            }
        },
    },
    plugins: [
        //require("daisyui")
    ],
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
    ],
    /*daisyui: {
        styled: true,
        themes: ['corporate', 'business'],
        base: true,
        utils: true,
        logs: true,
        rtl: false,
        darkTheme: "business",
    },*/
}

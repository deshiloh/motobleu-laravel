const colors = require('tailwindcss/colors')

module.exports = {
    darkMode: ['class', '[data-theme="dark"]'],
    content: [
        "./app/Components/**/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/wireui/wireui/src/*.php",
        "./vendor/wireui/wireui/ts/**/*.ts",
        "./vendor/wireui/wireui/src/WireUi/**/*.php",
        "./vendor/wireui/wireui/src/Components/**/*.php",
    ],
    theme: {
        extend: {
            colors: {
                'motobleu' : {
                    light: '#222ca1',
                    DEFAULT: '#0A158D',
                    'dark' : '#081068'
                },
                'secondary': colors.gray,
                'positive': colors.emerald,
                'negative': colors.red,
                'warning': colors.amber,
                'info': colors.blue
            }
        },
    },
    plugins: [],
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
    ],
}

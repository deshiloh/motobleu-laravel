module.exports = {
    darkMode: 'class',
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
        extend: {},
    },
    plugins: [
        require("daisyui")
    ],
    presets: [
        require('./vendor/wireui/wireui/tailwind.config.js')
    ],
    daisyui: {
        styled: true,
        themes: ['corporate', 'business'],
        base: true,
        utils: true,
        logs: true,
        rtl: false,
        darkTheme: "business",
    },
}

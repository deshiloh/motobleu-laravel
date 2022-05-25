require('./bootstrap');

import $ from 'jquery';
import 'flowbite';
import 'select2';

checkDarkMode()

let darkMode = document.querySelector('.dark-mode');

darkMode.addEventListener('click', function () {
    if (document.documentElement.classList.contains('dark')) {
        localStorage.theme = 'light'
        document.documentElement.classList.remove('dark')
    } else {
        localStorage.theme = 'dark'
        document.documentElement.classList.add('dark')
    }
})

function checkDarkMode() {
    if (
        localStorage.theme === 'dark' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        document.documentElement.classList.add('dark')
    } else {
        document.documentElement.classList.remove('dark')
    }
}

$(document).ready(function() {
    $('.js-example-basic-single').select2({
        language: "fr"
    });
});

// Whenever the user explicitly chooses light mode


// Whenever the user explicitly chooses dark mode

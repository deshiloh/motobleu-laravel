require('./bootstrap');
import 'alpinejs';
import 'flowbite';

checkDarkMode();

function checkDarkMode() {
    if (
        localStorage.theme === 'business' ||
        (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    ) {
        document.querySelector('.darkmode').removeAttribute('checked')
        document.querySelector('html').setAttribute('data-theme', 'business')
    } else {
        document.querySelector('.darkmode').setAttribute('checked', 'checked')
        document.querySelector('html').setAttribute('data-theme', 'corporate')
    }
}

// Whenever the user explicitly chooses dark mode

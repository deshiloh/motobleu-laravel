<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Motobleu</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="antialiased bg-gray-200 dark:bg-gray-800">
    @auth()
        <header class="text-gray-600 dark:text-gray-200 body-font">
            <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center shadow-sm bg-white dark:bg-gray-900 rounded-bl-md rounded-br-lg">
                <a class="flex title-font font-medium items-center text-gray-900 dark:text-gray-100 mb-4 md:mb-0" href="/">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-10 h-10 text-white p-2 bg-indigo-500 rounded-full" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                    <span class="ml-3 text-xl">Motobleu laravel</span>
                </a>
                <nav class="md:mr-auto md:ml-4 md:py-1 md:pl-4 md:border-l md:border-gray-400 flex flex-wrap items-center text-base justify-center">
                    <x-menu.item link="{{ route('admin.accounts.index') }}">Comptes</x-menu.item>
                </nav>
                <button class="dark-mode inline-flex items-center border-0 py-1 px-2 focus:outline-none rounded text-base mt-4 md:mt-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                @auth()
                    <a class="inline-flex items-center border-0 py-1 px-2 focus:outline-none rounded text-base mt-4 md:mt-0" href="{{ route('logout') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </a>
                @endauth
            </div>
        </header>
    @endauth
    <x-alerts></x-alerts>
    {{ $slot }}
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>


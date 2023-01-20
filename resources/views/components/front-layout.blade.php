<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Motobleu</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"
    />
    <wireui:scripts />
    <script src="//unpkg.com/alpinejs" defer></script>
    @stack('styles')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    @livewireStyles()
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body @class([
    'antialiased min-h-screen h-full',
    'bg-motobleu' => Route::currentRouteName() == 'front.home',
    'bg-gray-200' => Route::currentRouteName() != 'front.home'
    ])
>
    <x-notifications />
    <div class="min-h-screen h-full relative">
        <nav @class([
                'text-white z-10 relative',
                'bg-motobleu-dark' => Route::currentRouteName() == 'front.home',
                'bg-motobleu' => Route::currentRouteName() != 'front.home'
            ])
            x-data="{isOpen: false}"
            >
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-4 2xl:space-x-8">
                            <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
                            <x-menu.front />
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <div class="flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <a href="mailto:contact@motobleu-paris.com" class="transition transition-all hover:text-gray-400">contact@motobleu-paris.com</a>
                            <a href="{{ route('switch.local', ['locale' => 'fr']) }}"><span class="fi fi-fr"></a>
                            <a href="{{ route('switch.local', ['en']) }}"><span class="fi fi-gb"></a>
                            @auth
                                <a href="{{ route('logout') }}" class="p-1 rounded-full text-white hover:text-gray-400 transition transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="-mr-2 flex items-center sm:hidden">
                        <!-- Mobile menu button -->
                        <button @click="isOpen = !isOpen" type="button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <!--
                              Icon when menu is closed.

                              Heroicon name: outline/bars-3

                              Menu open: "hidden", Menu closed: "block"
                            -->
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <!--
                              Icon when menu is open.

                              Heroicon name: outline/x-mark

                              Menu open: "block", Menu closed: "hidden"
                            -->
                            <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div class="sm:hidden" id="mobile-menu" x-show="isOpen">
                <div class="space-y-1 pt-2">

                    <x-menu.front />

                    <div class="flex items-center space-x-4 py-4 justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <a href="mailto:contact@motobleu-paris.com" class="transition transition-all hover:text-gray-400">contact@motobleu-paris.com</a>
                        <a href="{{ route('switch.local', ['locale' => 'fr']) }}"><span class="fi fi-fr"></a>
                        <a href="{{ route('switch.local', ['en']) }}"><span class="fi fi-gb"></a>
                        @auth
                            <a href="{{ route('logout') }}" class="p-1 rounded-full text-white hover:text-gray-400 transition transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{ $slot }}
    </div>
    @livewireScripts()
    @stack('scripts')
</body>
</html>


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Motobleu</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/select2.css') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles()
</head>
<body class="antialiased bg-gray-200 dark:bg-gray-800">
    @auth()
        <nav class="bg-white border-gray-200 px-2 sm:px-4 py-2.5 rounded dark:bg-gray-900">
            <div class="container flex flex-wrap justify-between items-center mx-auto">
                <a href="/" class="flex items-center space-x-3 dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                    </svg>
                    <span class="self-center text-xl font-semibold whitespace-nowrap">Motobleu</span>
                </a>
                <button data-collapse-toggle="mobile-menu" type="button" class="inline-flex items-center p-2 ml-3 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                    <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
                <x-menu.index>
                    <x-menu.item-link href="{{ route('admin.accounts.index') }}">Comptes</x-menu.item-link>
                    <x-menu.item-link href="{{ route('admin.entreprises.index') }}">Entreprises</x-menu.item-link>
                    <x-menu.item-link href="{{ route('admin.pilotes.index') }}">Pilotes</x-menu.item-link>
                    <x-menu.dropdown title="Gestion">
                        <x-menu.dropdown.item-link href="{{ route('admin.passagers.index') }}">
                            Gestion passagers
                        </x-menu.dropdown.item-link>
                        <x-menu.dropdown.item-link href="{{ route('admin.localisations.index') }}">
                            Gestion localisations
                        </x-menu.dropdown.item-link>
                        <x-menu.dropdown.item-link href="{{ route('admin.adresse-reservation.index') }}">
                            Gestion des adresses
                        </x-menu.dropdown.item-link>
                        <x-menu.dropdown.item-link href="{{ route('admin.costcenter.index') }}">
                            Gestion Cost Center
                        </x-menu.dropdown.item-link>
                        <x-menu.dropdown.item-link href="{{ route('admin.typefacturation.index') }}">
                            Gestion Type facturation
                        </x-menu.dropdown.item-link>
                    </x-menu.dropdown>
                    <x-menu.item-link href="{{ route('admin.reservations.index') }}">Réservations</x-menu.item-link>
                    <x-menu.item>
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
                    </x-menu.item>
                </x-menu.index>
            </div>
        </nav>
    @endauth
    {{ $slot }}
    <wireui:scripts />
    <script defer src="https://unpkg.com/alpinejs@3.10.2/dist/cdn.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts()
    @stack('scripts')
</body>
</html>


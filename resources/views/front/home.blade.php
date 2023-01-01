<x-front-layout>
    <nav class="bg-motobleu shadow text-white">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <div class="flex flex-shrink-0 items-center">
                        <img class="block h-8 w-auto lg:hidden" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
                        <img class="hidden h-8 w-auto lg:block" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
                        <a href="#" class="inline-flex items-center border-b-2 border-indigo-900 px-1 pt-1 text-sm font-medium text-white">Dashboard</a>
                        <a href="#" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Team</a>
                        <a href="#" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Projects</a>
                        <a href="#" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Calendar</a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <a href="#">contact@motobleu-paris.com</a>
                    </div>

                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <!-- Mobile menu button -->
                    <button type="button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
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
        <div class="sm:hidden" id="mobile-menu">
            <div class="space-y-1 pt-2 pb-3">
                <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
                <a href="#" class="block border-l-4 border-indigo-500 bg-indigo-50 py-2 pl-3 pr-4 text-base font-medium text-indigo-700">Dashboard</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Team</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Projects</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Calendar</a>
            </div>
            <div class="border-t border-gray-200 pt-4 pb-3">
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium text-gray-800">Tom Cook</div>
                        <div class="text-sm font-medium text-gray-500">tom@example.com</div>
                    </div>
                    <button type="button" class="ml-auto flex-shrink-0 rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">View notifications</span>
                        <!-- Heroicon name: outline/bell -->
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800">Your Profile</a>
                    <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800">Settings</a>
                    <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800">Sign out</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="h-[53rem] grid grid-cols-2 relative">
        <div class="absolute w-full h-full flex items-center justify-center z-10">
            <div class="max-w-4xl">
                <h1>
                    <img src="{{ asset('storage/motobleu-logo.png') }}" alt="Motobleu-Paris">
                </h1>
                <p class="text-center text-4xl text-white py-4">Transport de personne à moto</p>
                <div class="grid grid-cols-2 gap-10">
                    <div class="flex justify-end">
                        <a href="#" class="bg-motobleu inline-block px-10 py-2 rounded text-center text-white">
                            Nouveau client ? <br>
                            <span class="font-bold">Enregistrez-vous</span>
                        </a>
                    </div>
                    <div class="flex justify-start">
                        <a href="#" class="bg-white inline-block px-10 py-2 text-center rounded">
                            Déjà client ? <br>
                            <span class="font-bold">Connectez-vous</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative" id="slider1"
             x-data="{
                currentIndex: 0,
                nbElement: document.querySelectorAll('#slider1 .sliderItem ').length
             }"
            x-init="
            setInterval(function() {
                currentIndex ++;
                if (currentIndex > nbElement - 1) { currentIndex = 0; }
                console.log(currentIndex);
            }, 5000)
            "
        >
            @for($i = 0; $i < 3; $i ++)
                <img
                    x-show="currentIndex == {{ $i }}"
                    src="https://picsum.photos/id/1{{ $i }}/2000/2000"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover z-0 sliderItem"
                    x-transition
                >
            @endfor
        </div>
        <div class="bg-motobleu"></div>
    </div>
    <div class="h-[53rem] grid grid-cols-2 relative">
        <div class="bg-motobleu"></div>
        <div class="relative" id="slider2"
             x-data="{
                currentIndex: 0,
                nbElement: document.querySelectorAll('#slider2 .sliderItem ').length
             }"
             x-init="
            setInterval(function() {
                currentIndex ++;
                if (currentIndex > nbElement - 1) { currentIndex = 0; }
                console.log(currentIndex);
            }, 5000)
            "
        >
            @for($i = 0; $i < 3; $i ++)
                <img
                    x-show="currentIndex == {{ $i }}"
                    src="https://picsum.photos/id/1{{ $i }}/2000/2000"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover z-0 sliderItem"
                    x-transition
                >
            @endfor
        </div>
    </div>
    <div class="h-[53rem] grid grid-cols-2 regex">
        <div class="bg-red-500"></div>
        <div class="bg-white"></div>
    </div>
</x-front-layout>

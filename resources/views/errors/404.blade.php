<x-front-layout>
    <div class="flex h-full flex-col bg-white absolute inset-0 z-0">
        <div class="flex flex-grow flex-col">
            <main class="flex flex-grow flex-col bg-white">
                <div class="mx-auto flex w-full max-w-7xl flex-grow flex-col px-6 lg:px-8">
                    <div class="flex-shrink-0 pt-10 sm:pt-16"></div>
                    <div class="my-auto flex-shrink-0 py-16 sm:py-32">
                        <p class="text-base font-semibold text-indigo-600">404</p>
                        <h1 class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">Page not found</h1>
                        <p class="mt-2 text-base text-gray-500">Sorry, we couldn’t find the page you’re looking for.</p>
                        <div class="mt-6">
                            <a href="{{ route('front.home') }}" class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                                Go back home
                                <span aria-hidden="true"> &rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="hidden lg:absolute lg:inset-y-0 lg:right-0 lg:block lg:w-1/2">
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1470847355775-e0e3c35a9a2c?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1825&q=80" alt="">
        </div>
    </div>
</x-front-layout>

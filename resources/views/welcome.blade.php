<x-layout>
    @guest()
        <div class="flex flex-col items-center justify-center bg-gray-200 h-screen w-screen dark:bg-gray-700">
            <div class="container bg-white dark:bg-gray-900 max-w-lg mx-auto rounded-lg p-5 shadow-sm dark:text-gray-100">
                <h3 class="text-xl flex justify-between">
                    <span>Connexion</span>
                    <button class="dark-mode inline-flex items-center border-0 py-1 px-2 focus:outline-none rounded text-base mt-4 md:mt-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </h3>
                <x-form route="{{ route('login') }}" method="POST">
                    <x-form.input type="email" label="Email" name="email" required="true"></x-form.input>
                    <x-form.input type="password" label="Mot de passe" name="password" required="true"></x-form.input>
                    <x-slot name="extra">
                        <a class="mt-2 ml-3 text-sm" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                    </x-slot>
                    @section('extra')
                        <a href="{{ route('password.request') }}" class="mt-2 ml-2">Mot de passe oublié ?</a>
                    @endsection
                </x-form>
            </div>
        </div>
    @endguest
</x-layout>

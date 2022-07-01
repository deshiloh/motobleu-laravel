<x-layout>
    @guest()
        <section class="h-screen dark:bg-gray-800 flex items-center justify-center">
            <div class="dark:text-white dark:bg-gray-900 rounded-lg p-9 shadow-md w-full max-w-2xl">
                <div class="flex items-center flex-col space-y-5 mb-8">
                    <div class="logo">
                        <img class="h-14 w-14" src="https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg" alt="Workflow">
                    </div>
                    <div class="text-2xl">Motobleu-Paris Application</div>
                </div>
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input label="Adresse email" icon="mail" name="email" />
                        </div>
                        <div>
                            <x-input label="Mot de passe" icon="key" name="password" />
                        </div>
                    </div>
                    <div class="mt-8">
                        <x-button label="Connexion" class="w-full" info type="submit" />
                    </div>
                </form>
                <div class="flex justify-center my-4">
                    <a class="dark:text-gray-400" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                </div>
                <div class="mt-20 flex items-center justify-center">
                    <span class="mr-6">Vous n'avez pas de compte ? </span> <x-button label="Demande de compte" sm />
                </div>
            </div>
        </section>
        @else
        <div class="container mx-auto grid grid-cols-2 my-5 shadow-sm dark:text-white">
            @if($reservations_to_confirm > 0)
                <a href="{{ route('admin.reservations.index', ['querySort' => 'not_confirmed']) }}" class="bg-white dark:bg-gray-900 border dark:border-black border-gray-200 p-7 rounded-tl-lg rounded-bl-lg border-r-0 dark:hover:bg-red-900/10 transition-all duration-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="block text-lg">Réservations à valider</span>
                            <span class="text-4xl text-red-600 font-bold">{{ $reservations_to_confirm }}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </a>
                @else
                <div class="bg-white dark:bg-gray-900 border dark:border-black border-gray-200 p-7 rounded-tl-lg rounded-bl-lg border-r-0">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="block text-lg">Réservations à valider</span>
                            <span class="text-4xl font-bold">0</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-black p-7 rounded-tr-lg rounded-br-lg">
                <span class="block text-lg">Réservations au total</span>
                <span class="text-4xl font-bold">{{ $reservations }}</span>
            </div>
        </div>
    @endguest
</x-layout>

<x-layout>
    @guest()
        <div class="hero min-h-screen bg-base-200">
            <div class="hero-content w-full max-w-lg">
                <div class="card w-full shadow-2xl bg-base-100">
                    <div class="card-body">
                        <div class="flex justify-end">
                            <x-darkmode :size="6"/>
                        </div>
                        <div class="text-2xl text-center border-b border-gray-500 pb-4">Motobleu Connexion</div>
                        <div class="flex flex-col w-full border-opacity-50">
                            <div>
                                <form action="{{ route('login') }}" method="post">
                                    @csrf
                                    <div class="space-y-4">
                                        <x-input label="Adresse email" icon="mail" name="email"/>
                                        <x-input label="Mot de passe" icon="key" name="password"/>
                                    </div>
                                    <div class="mt-8">
                                        <button class="btn btn-primary w-full" type="submit">Connexion</button>
                                    </div>
                                </form>
                                <div class="flex justify-center py-3">
                                    <a class="text-sm" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                                </div>
                            </div>
                            <div class="divider">OU</div>
                            <div class="flex justify-center flex-col">
                                <a href="#" class="btn w-full">Demande de compte</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container mx-auto my-4">
            <div class="stats shadow">

                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="stat-title">Réservations</div>
                    <div class="stat-value">31K</div>
                    <div class="stat-desc">Jan 1st - Feb 1st</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </div>
                    <div class="stat-title">À confirmer</div>
                    <div class="stat-value">{{ $reservations_to_confirm }}</div>
                    {{--@if($reservations_to_confirm > 0)
                        <a class="btn btn-xs btn-ghost gap-2" href="{{ route('admin.reservations.index', ['querySort' => 'not_confirmed']) }}">
                            Voir les réservations
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif--}}
                </div>

                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <div class="stat-title">New Registers</div>
                    <div class="stat-value">1,200</div>
                    <div class="stat-desc">↘︎ 90 (14%)</div>
                </div>

            </div>

            <div class="stats shadow">

                <div class="stat place-items-center">
                    <div class="stat-title">Réservation au total</div>
                    <div class="stat-value">{{ $reservations }}</div>
                </div>

                <div @class([
                    'stat place-items-center',
                    'bg-warning text-warning-content' => $reservations_to_confirm > 0
                ])>
                    <div class="stat-title">Réservations à confirmer</div>
                    <div class="stat-value">{{ $reservations_to_confirm }}</div>
                    @if($reservations_to_confirm > 0)
                        <a class="btn btn-xs btn-ghost gap-2" href="{{ route('admin.reservations.index', ['querySort' => 'not_confirmed']) }}">
                            Voir les réservations
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                </div>

                <div class="stat place-items-center">
                    <div class="stat-title">New Registers</div>
                    <div class="stat-value">1,200</div>
                    <div class="stat-desc">↘︎ 90 (14%)</div>
                </div>

            </div>
        </div>
        <div class="container mx-auto grid grid-cols-2 my-5 shadow-sm dark:text-white">
            @if($reservations_to_confirm > 0)
                <a href="{{ route('admin.reservations.index', ['querySort' => 'not_confirmed']) }}"
                   class="bg-white dark:bg-gray-900 border dark:border-black border-gray-200 p-7 rounded-tl-lg rounded-bl-lg border-r-0 dark:hover:bg-red-900/10 transition-all duration-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="block text-lg">Réservations à valider</span>
                            <span class="text-4xl text-red-600 font-bold">{{ $reservations_to_confirm }}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-red-600" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @else
                <div
                    class="bg-white dark:bg-gray-900 border dark:border-black border-gray-200 p-7 rounded-tl-lg rounded-bl-lg border-r-0">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="block text-lg">Réservations à valider</span>
                            <span class="text-4xl font-bold">0</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
            <div
                class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-black p-7 rounded-tr-lg rounded-br-lg">
                <span class="block text-lg">Réservations au total</span>
                <span class="text-4xl font-bold">{{ $reservations }}</span>
            </div>
        </div>
    @endguest
</x-layout>

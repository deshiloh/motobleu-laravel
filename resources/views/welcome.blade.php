<x-layout>
    @guest()
        <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white dark:bg-slate-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="flex justify-center py-2">
                        <x-darkmode :size="6"/>
                    </div>
                    <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-gray-200 mb-6">{{ config('app.name') }}</h2>
                    <form class="space-y-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div>
                            <div class="mt-1">
                                <x-input label="Adresse email" placeholder="Votre adresse email..." icon="mail" name="email" />
                            </div>
                        </div>

                        <div>
                            <div class="mt-1">
                                <x-input label="Mot de passe" icon="key" name="password" type="password"/>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            {{--<div class="flex items-center">
                                <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="remember-me" class="ml-2 block text-sm text-gray-900"> Remember me </label>
                            </div>--}}

                            <div class="text-sm">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500"> Mot de passe oublié ? </a>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Se connecter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{--<div class="hero min-h-screen bg-base-200">
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
                                        <x-input label="Mot de passe" icon="key" name="password" type="password"/>
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
        </div>--}}
    @else
        {{--<div class="container mx-auto my-4">
            <div class="stats shadow">

                <div class="stat place-items-center">
                    <div class="stat-title">Réservation au total</div>
                    <div class="stat-value">{{ $reservations }}</div>
                </div>

                <div @class([
                    'stat place-items-center',
                    'bg-warning text-warning-content' => $reservations_to_confirm->count() > 0
                ])>
                    <div class="stat-title">Réservations à confirmer</div>
                    <div class="stat-value">{{ $reservations_to_confirm->count() }}</div>
                    @if($reservations_to_confirm->count() > 0)
                        <a class="btn btn-xs btn-ghost gap-2" href="{{ route('admin.reservations.index', ['querySort' => 'not_confirmed']) }}">
                            Voir les réservations
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @endif
                </div>

                <div class="stat place-items-center">
                    <div class="stat-title">Utilisateurs</div>
                    <div class="stat-value">{{ $users }}</div>
                </div>

            </div>
        </div>
        <div class="container mx-auto">
            <x-admin.content>
                <div class="container mx-auto">
                    <div class="text-xl mb-4">Réservations à confirmer</div>
                    <x-datatable>
                        <x-slot:headers>
                            <x-datatable.tr>
                                <x-datatable.th>Référence</x-datatable.th>
                                <x-datatable.th>Date</x-datatable.th>
                                <x-datatable.th>Départ</x-datatable.th>
                                <x-datatable.th>Arrivée</x-datatable.th>
                                <x-datatable.th>Entreprise</x-datatable.th>
                                <x-datatable.th>Actions</x-datatable.th>
                            </x-datatable.tr>
                        </x-slot:headers>
                        <x-slot:body>
                            @foreach($reservations_to_confirm->get() as $reservation)
                                <x-datatable.tr>
                                    <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                                    <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                                    <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                                    <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                                    <x-datatable.td>{{ $reservation->passager->user->entreprise->nom }}</x-datatable.td>
                                    <x-datatable.td>
                                        <a href="{{ route('admin.reservations.show', ['reservation' => $reservation->id]) }}" class="btn btn-primary btn-sm">
                                            Détails
                                        </a>
                                    </x-datatable.td>
                                </x-datatable.tr>
                            @endforeach
                        </x-slot:body>
                    </x-datatable>
                </div>
            </x-admin.content>
        </div>--}}

    @endguest
</x-layout>

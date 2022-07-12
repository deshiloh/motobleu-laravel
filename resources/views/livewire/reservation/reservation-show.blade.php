<div class="pb-6">
    @if(!$reservation->is_confirmed && !$reservation->is_cancel)
        <div class="alert alert-error shadow-lg my-4">
            <div>
                <div class="flex flex-col">
                    <div class="flex items-center space-x-2">
                        <x-carbon-warning-alt class="h-6 w-6"/>
                        <span class="font-bold">Cette réservation n'as pas été confirmée</span>
                    </div>
                    <div class="mt-3">
                        Afin de la confirmer vous devez remplir le formulaire ci-dessous
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if($reservation->is_confirmed && !$reservation->is_cancel)
        <div class="alert alert-success my-4">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Cette réservation est confirmée</span>
            </div>
        </div>
    @endif
    @if($reservation->is_cancel)
        <div id="alert-additional-content-2" class="p-4 my-4 bg-red-100 rounded-lg dark:bg-red-200" role="alert">
            <div class="flex items-center">
                <svg class="mr-2 w-5 h-5 text-red-700 dark:text-red-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                <h3 class="text-lg font-medium text-red-700 dark:text-red-800">Cette réservation a été annulée</h3>
            </div>
        </div>
    @endif
    <x-title-section>
        <x-slot:title>
            Réservation : <span class="text-blue-500">{{ $reservation->reference }}</span>
        </x-slot:title>
        @if(!is_null($reservation->event_id))
            <x-button
                href="{{ $reservation->getEvent()->getHtmlLink() }}"
                target="_blank"
                label="Google Agenda"
                primary
                icon="calendar"
            />
        @endif
        <a href="{{ route('admin.reservations.edit', ['reservation' => $reservation->id]) }}" class="btn btn-primary btn-sm">
            Éditer
        </a>
        @if(!$reservation->is_cancel)
            <button class="btn gap-2 btn-warning btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                Annuler mais facturer
            </button>
            <button class="btn gap-2 btn-error btn-sm" wire:click="cancel">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Annuler
            </button>
        @endif
    </x-title-section>

    @if(!$reservation->is_confirmed && !$reservation->is_cancel)
        <x-admin.content>
        <form wire:submit.prevent="confirmedAction" action="post" wire:loading.class="opacity-25" class="space-y-4">
            @csrf
            <div class="block text-xl">Formulaire de confirmation</div>
            <x-select
                label="Pilote"
                placeholder="Sélectionner un pilote"
                :async-data="route('api.pilotes')"
                option-label="full_name"
                option-value="id"
                option-description="email"
                wire:model.defer="reservation.pilote_id"
            />
            <x-textarea label="Message" placeholder="Votre message..." wire:model="message"/>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    Emails de confirmation
                    <div class="space-y-3 pl-3">
                        <x-form.toggle wire:model="reservation.send_to_user">
                            Secrétaire : {{ $reservation->passager->user->full_name }}
                        </x-form.toggle>
                        <x-form.toggle wire:model="reservation.send_to_passager">
                            Passager : {{ $reservation->passager->nom }}
                        </x-form.toggle>
                    </div>
                </div>
                <div>
                    Invitation Google Calendar
                    <div class="space-y-3 pl-3">
                        <x-form.toggle wire:model.defer="reservation.calendar_user_invitation">
                            Secrétaire : {{ $reservation->passager->user->full_name }}
                        </x-form.toggle>
                        <x-form.toggle wire:model.defer="reservation.calendar_passager_invitation">
                            Passager : {{ $reservation->passager->nom }}
                        </x-form.toggle>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                Valider et envoyer le message
            </button>
        </form>
        </x-admin.content>
    @endif

    <x-admin.content>
        <div class="text-xl">Détails de la course</div>
        <span class="text-sm">Créer le {{ $reservation->created_at->format('d/m/Y H:i') }}</span>
        <div class="bg-base-200 text-center p-3 rounded-lg mt-2">
            <div class="text-4xl">{{ $reservation->pickup_date->format('d/m/Y à H:i') }}</div>
            <div class="text-xl">Date de la réservation</div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div class="bg-base-200 rounded-lg p-3">
                <div class="text-xl">Départ</div>
                <div class="text-2xl">
                    {{ $reservation->display_from }}
                </div>
            </div>
            <div class="bg-base-200 rounded-lg p-3">
                <div class="text-xl">Arrivée</div>
                <div class="text-2xl">
                    {{ $reservation->display_to }}
                </div>
            </div>
        </div>
    </x-admin.content>
    <x-admin.content>
        <div class="overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-base-content">Client</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations sur le client</p>
            </div>
            <div class="border-t border-secondary/40 dark:border-gray-600">
                <dl>
                    <div class="bg-base-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nom - Prénom</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->user->full_name }}</dd>
                    </div>
                    <div class="bg-base-200 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Entreprise</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->user->entreprise->nom }}</dd>
                    </div>
                    <div class="bg-base-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Adresse email</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->user->email }}</dd>
                    </div>
                    <div class="bg-base-200 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->user->telephone }}</dd>
                    </div>
                    <div class="bg-base-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Portable</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->user->portable }}</dd>
                    </div>
                    <div class="bg-base-200 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Adresses</dt>
                        <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                            <ul role="list" class="rounded-md divide-y divide-gray-200">
                                @foreach($reservation->passager->user->entreprise->adresseEntreprises()->get() as $adresse)
                                    <div class="card w-96 bg-base-100">
                                        <div class="card-body">
                                            <h2 class="card-title">{{ $adresse->type->name }}</h2>
                                            <p>
                                                {{ $adresse->email }}
                                            </p>
                                            <p>
                                            {{ $adresse->adresse_full }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </x-admin.content>
    <x-admin.content>
        <div class="overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-base-content">Passager</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations sur le passager</p>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-600">
                <dl>
                    <div class="bg-base-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Nom</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->nom }}</dd>
                    </div>
                    <div class="bg-base-200 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Adresse email</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->email }}</dd>
                    </div>
                    <div class="bg-base-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->telephone }}</dd>
                    </div>
                    <div class="bg-base-200 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Portable</dt>
                        <dd class="mt-1 text-sm text-base-content sm:mt-0 sm:col-span-2">{{ $reservation->passager->portable }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </x-admin.content>
    @if($reservation->pilote()->exists())
        <x-admin.content wire:key="pilote_details">
            <div class="overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium dark:text-gray-100 text-gray-900">Pilote</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations sur le pilote</p>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-600">
                    <dl>
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nom</dt>
                            <dd class="mt-1 text-sm dark:text-gray-200 text-gray-900 sm:mt-0 sm:col-span-2">{{ $reservation->pilote->full_name }}</dd>
                        </div>
                        <div class="bg-white dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Adresse email</dt>
                            <dd class="mt-1 text-sm dark:text-gray-200 text-gray-900 sm:mt-0 sm:col-span-2">{{ $reservation->pilote->email }}</dd>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                            <dd class="mt-1 text-sm dark:text-gray-200 text-gray-900 sm:mt-0 sm:col-span-2">{{ $reservation->pilote->telephone }}</dd>
                        </div>
                        <div class="bg-white dark:bg-gray-900 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Entreprise</dt>
                            <dd class="mt-1 text-sm dark:text-gray-200 text-gray-900 sm:mt-0 sm:col-span-2">{{ $reservation->pilote->entreprise }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </x-admin.content>
    @endif
</div>

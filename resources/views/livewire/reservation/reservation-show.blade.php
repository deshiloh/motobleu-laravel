<div class="pb-6">
    <x-header>
        Réservation : <span class="text-blue-500">{{ $reservation->reference }}</span>
        <x-slot:right>
            <div class="flex items-center justify-center space-x-2">
                @if(!is_null($reservation->event_id))
                    <x-button icon="calendar" href="{{ $reservation->getEvent()->getHtmlLink() }}" target="_blank" label="Google Agenda" info />
                @endif
                <x-button href="{{ route('admin.reservations.edit', ['reservation' => $reservation->id]) }}"  icon="pencil-alt" primary label="Éditer" />
                @if(!$reservation->is_cancel)
                    <x-button warning label="Annuler mais facturer" icon="credit-card"/>
                    <x-button wire:click="cancelAction" negative label="Annuler" icon="x-circle" wire:key="cancelAction"/>
                @endif
            </div>
        </x-slot:right>
    </x-header>
    <x-center-bloc class="mb-4">
        @if(!$reservation->is_confirmed && !$reservation->is_cancel)
            <div class="rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: solid/exclamation -->
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Cette réservation n'as pas été confirmée</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Afin de la confirmer vous devez remplir le formulaire ci-dessous</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($reservation->is_confirmed && !$reservation->is_cancel)
            <div class="rounded-md bg-green-50 dark:bg-green-200 p-4 mb-4 shadow dark:shadow-none">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400 dark:text-green-900" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-800">Cette réservation est confirmée</p>
                    </div>
                </div>
            </div>
        @endif
        @if($reservation->is_cancel)
            <div class="rounded-md bg-red-100 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">Cette réservation est annulée</p>
                    </div>
                </div>
            </div>
        @endif
    </x-center-bloc>
    <x-bloc-content>
        <div class="space-y-4">
            <div class="block text-xl dark:text-white">Formulaire de confirmation</div>
            @csrf
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
                    <div class="dark:text-white">Emails de confirmation</div>
                    <div class="space-y-3 mt-3">
                        <x-toggle wire:model="reservation.send_to_user" label="Secrétaire : {{ $reservation->passager->user->full_name }}" md />
                        <x-toggle wire:model="reservation.send_to_passager" label="Passager : {{ $reservation->passager->nom }}" md />
                    </div>
                </div>
                <div>
                    <div class="dark:text-white">Invitation Google Calendar</div>
                    <div class="space-y-3 mt-3">
                        <x-toggle wire:model.defer="reservation.calendar_user_invitation" label="Secrétaire : {{ $reservation->passager->user->full_name }}" md />
                        <x-toggle wire:model.defer="reservation.calendar_passager_invitation" label="Passager : {{ $reservation->passager->nom }}" md />
                    </div>
                </div>
            </div>
            @if($reservation->pilote()->exists())
                <x-button label="Mettre à jour le pilote" primary sm wire:loading.attr="disabled" wire:click="updatePilote" spinner="updatePilote"/>
            @else
                <x-button label="Valider et envoyer le message" primary sm wire:click="confirmedAction" spinner="confirmedAction"/>
            @endif
        </div>
    </x-bloc-content>
    <div class="space-y-4">
        <x-center-bloc>
            <x-simple-card title="Détails de la réservation" description="Créer le {{ $reservation->created_at->format('d/m/Y H:i') }}">
                <x-simple-card.item title="Date de la réservation">
                    {{ $reservation->pickup_date->format('d/m/Y à H:i') }}
                </x-simple-card.item>
                <x-simple-card.item title="Départ">
                    {{ $reservation->display_from }}
                </x-simple-card.item>
                <x-simple-card.item title="Arrivée">
                    {{ $reservation->display_to }}
                </x-simple-card.item>
            </x-simple-card>
        </x-center-bloc>

        <x-center-bloc>
            <x-simple-card title="Client" description="Informations sur le client.">
                <x-simple-card.item title="Nom / Prénom">
                    {{ $reservation->passager->user->full_name }}
                </x-simple-card.item>

                <x-simple-card.item title="Entreprise">
                    {{ $reservation->entreprise->nom }}
                </x-simple-card.item>

                <x-simple-card.item title="Adresse email">
                    {{ $reservation->passager->user->email }}
                </x-simple-card.item>

                <x-simple-card.item title="Téléphone">
                    {{ $reservation->passager->user->telephone }}
                </x-simple-card.item>

                <x-simple-card.item title="Portable">
                    {{ $reservation->passager->user->portable ?? 'Non renseigné' }}
                </x-simple-card.item>

                <x-simple-card.item title="Adresses">
                    <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                        @foreach($reservation->entreprise->adresseEntreprises()->get() as $adresse)
                            <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                <div class="w-0 flex-1 flex items-center">
                                    @if($adresse->type->value === 0)
                                        <div class="flex space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span>Physique : </span>
                                        </div>
                                    @else
                                        <div class="flex space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Facturation : </span>
                                        </div>
                                    @endif
                                    <span class="ml-2 flex-1 w-0 truncate">{{ $adresse->adresse_full }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-simple-card.item>
            </x-simple-card>
        </x-center-bloc>

        <x-center-bloc>
            <x-simple-card title="Passager" description="Informations sur le passager">
                <x-simple-card.item title="Nom">
                    {{ $reservation->passager->nom }}
                </x-simple-card.item>

                <x-simple-card.item title="Adresse email">
                    {{ $reservation->passager->email }}
                </x-simple-card.item>

                <x-simple-card.item title="Téléphone">
                    {{ $reservation->passager->telephone }}
                </x-simple-card.item>

                <x-simple-card.item title="Portable">
                    {{ $reservation->passager->portable ?? 'Non renseigné.'}}
                </x-simple-card.item>
            </x-simple-card>
        </x-center-bloc>

        @if($reservation->pilote()->exists())
            <x-center-bloc wire:key="pilote_details">
                <x-simple-card title="Pilote" description="Informations sur le pilote">
                    <x-simple-card.item title="Nom">
                        {{ $reservation->pilote->full_name }}
                    </x-simple-card.item>
                    <x-simple-card.item title="Adresse email">
                        {{ $reservation->pilote->email }}
                    </x-simple-card.item>
                    <x-simple-card.item title="Téléphone">
                        {{ $reservation->pilote->telephone }}
                    </x-simple-card.item>
                    <x-simple-card.item title="Entreprise">
                        {{ $reservation->pilote->entreprise }}
                    </x-simple-card.item>
                </x-simple-card>
            </x-center-bloc>
        @endif
    </div>
</div>

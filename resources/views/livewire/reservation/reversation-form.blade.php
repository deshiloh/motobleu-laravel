<div>
    <x-header>
        Formulaire de réservation {{ $reservation->reference ?? '' }}
    </x-header>
    <div class="container mx-auto sm:px-6 lg:px-8">
        <x-errors class="mb-3"/>
    </div>
    <form wire:submit.prevent="saveReservation" wire:loading.class="opacity-25" wire:key="form_reservation">
        @if(!$reservation->exists)
            <x-bloc-content>
                <div class="flex flex-col space-y-3">
                    <div class="dark:text-white block">
                        Réservation avec retour :
                    </div>
                    <div>
                        <x-toggle wire:model="hasBack" left-label="Non" label="Oui" md/>
                    </div>
                </div>
            </x-bloc-content>
        @endif
        <x-bloc-content>
            <div class="space-y-3">
                <x-select
                    label="Secrétaire *"
                    placeholder="Sélectionner une secrétaire"
                    :async-data="route('api.users')"
                    option-label="full_name"
                    option-value="id"
                    option-description="entreprise.nom"
                    wire:model="userId"
                />
                <x-select
                    label="Entreprise rattachée *"
                    placeholder="Sélectionner une entreprise"
                    :async-data="route('api.entreprises_users', ['userId' => $userId])"
                    option-label="nom"
                    option-value="id"
                    wire:model="reservation.entreprise_id"
                />
                @if(!is_null($reservation->entreprise_id) && !in_array($reservation->entreprise_id, app(\app\Settings\BillSettings::class)->entreprise_without_command_field))
                    <x-input label="Numéro De commande / Case code" class="mb-3" wire:model.defer="reservation.commande"/>
                @endif
            </div>
        </x-bloc-content>
        <x-bloc-content>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">
                    Passager :
                </div>

                <div class="flex space-x-3">
                    <x-radio wire:model="passagerMode"
                             value="{{ \App\Services\ReservationService::EXIST_PASSAGER }}" label="Passager existant"/>
                    <x-radio wire:model="passagerMode"
                             value="{{ \App\Services\ReservationService::NEW_PASSAGER }}"
                             label="Créer un nouveau passager"/>
                </div>

                @if($passagerMode == \App\Services\ReservationService::EXIST_PASSAGER)
                    <x-select
                        wire:key="passanger_choice"
                        label="Passager existant"
                        placeholder="Sélectionner un passager"
                        :async-data="route('api.passagers', ['user' => $userId])"
                        option-label="nom"
                        option-value="id"
                        option-description="email"
                        wire:model.defer="reservation.passager_id"
                    />
                @endif

                @if($passagerMode == \App\Services\ReservationService::NEW_PASSAGER)
                    <div class="space-y-4">
                        <x-input label="Nom et prénom" wire:model="newPassager.nom"/>
                        <x-input label="Téléphone de bureau" wire:model="newPassager.telephone"/>
                        <x-input label="Téléphone portable" wire:model="newPassager.portable"/>
                        <x-input type="email" label="Adresse email" wire:model="newPassager.email"/>
                        @if(!is_null($reservation->entreprise_id) && in_array($reservation->entreprise_id, app(\app\Settings\BillSettings::class)->entreprises_cost_center_facturation))
                            <x-select
                                wire:key="cost_center"
                                label="Cost Center"
                                placeholder="Sélectionner un Cost Center"
                                :async-data="route('api.cost_center')"
                                option-label="nom"
                                option-value="id"
                                wire:model="newPassager.cost_center_id"
                            />
                            <x-select
                                wire:key="type_facturation"
                                label="Type de facturation"
                                placeholder="Sélectionner un type de facturation"
                                :async-data="route('api.type_facturation')"
                                option-label="nom"
                                option-value="id"
                                wire:model="newPassager.type_facturation_id"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </x-bloc-content>
        <x-bloc-content>
            <x-datetime-picker
                wire:key="pickup_date"
                label="Date de prise en charge"
                placeholder="Choisir une date"
                display-format="DD/MM/YYYY HH:mm"
                time-format="24"
                interval="1"
                wire:model="reservation.pickup_date"
                :without-timezone="true"
            />
        </x-bloc-content>

        <x-bloc-content>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">Départ :</div>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroports ou gares"/>
                    <x-radio wire:model="pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                    <x-radio wire:model="pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                             label="Créer une nouvelle adresse"/>
                </div>

                @if($pickupMode == \App\Services\ReservationService::WITH_PLACE)
                    <div class="space-y-4">
                        <x-select
                            wire:key="from_place"
                            label="Aéroports ou gares"
                            placeholder="Sélectionnez une gare ou un aéroport"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="reservation.localisation_from_id"
                        />
                        @if($reservation->localisation_from_id)
                            <x-input label="Provenance / N°" wire:model="reservation.pickup_origin" />
                        @endif
                    </div>
                @endif

                @if($pickupMode == \App\Services\ReservationService::WITH_ADRESSE)
                    <x-select
                        wire:key="from_adresse"
                        label="Adresse"
                        placeholder="Sélectionner une adresse"
                        :async-data="route('api.adresses', ['user' => $userId])"
                        option-label="full_adresse"
                        option-value="id"
                        wire:model="reservation.adresse_reservation_from_id"
                    />
                @endif

                @if($pickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                    <div class="space-y-4">
                        <x-input label="Adresse" wire:model.defer="newAdresseReservationFrom.adresse"/>
                        <x-input label="Adresse complémentaire"
                                 wire:model.defer="newAdresseReservationFrom.adresse_complement"/>
                        <x-input label="Code postal" wire:model.defer="newAdresseReservationFrom.code_postal"/>
                        <x-input label="Ville" wire:model.defer="newAdresseReservationFrom.ville"/>
                    </div>
                @endif
            </div>
        </x-bloc-content>
        <x-bloc-content>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">Arrivée :</div>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="dropMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroports ou gares"/>
                    <x-radio wire:model="dropMode"
                             value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                    <x-radio wire:model="dropMode"
                             value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                             label="Créer une nouvelle adresse"/>
                </div>

                @if($dropMode == \App\Services\ReservationService::WITH_PLACE)
                    <div class="space-y-4">
                        <x-select
                            wire:key="to_place"
                            label="Aéroports ou gares"
                            placeholder="Aéroports ou gares"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="reservation.localisation_to_id"
                        />
                        @if($reservation->localisation_to_id)
                            <div class="form-group">
                                <x-input label="Provenance / N°" wire:model="reservation.drop_off_origin"/>
                            </div>
                        @endif
                    </div>
                @endif

                @if($dropMode == \App\Services\ReservationService::WITH_ADRESSE)
                    <x-select
                        wire:key="to_adresse"
                        label="Adresse"
                        placeholder="Sélectionner une adresse"
                        :async-data="route('api.adresses', ['user' => $userId])"
                        option-label="full_adresse"
                        option-value="id"
                        wire:model="reservation.adresse_reservation_to_id"
                    />
                @endif

                @if($dropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                    <div class="space-y-4">
                        <x-input label="Adresse" wire:model.defer="newAdresseReservationTo.adresse"/>
                        <x-input label="Adresse complémentaire"
                                 wire:model.defer="newAdresseReservationTo.adresse_complement"/>
                        <x-input label="Code postal" wire:model.defer="newAdresseReservationTo.code_postal"/>
                        <x-input label="Ville" wire:model.defer="newAdresseReservationTo.ville"/>
                    </div>
                @endif
            </div>
        </x-bloc-content>

        <x-bloc-content>
            <x-textarea placeholder="Votre commentaire..." wire:model.defer="reservation.comment" label="Commentaire"/>
        </x-bloc-content>

        @if($hasBack)
            <div class="sm:px-6 lg:px-4 mb-4">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-gray-100 px-2 text-sm text-gray-500 text-2xl">Réservation retour</span>
                    </div>
                </div>
            </div>

            <x-bloc-content>
                <x-datetime-picker
                    wire:key="back_date_picker"
                    label="Date"
                    placeholder="Choisir une date"
                    display-format="DD/MM/YYYY HH:mm"
                    time-format="24"
                    interval="1"
                    wire:model="reservation_back.pickup_date"
                    :without-timezone="true"
                />
            </x-bloc-content>

            <x-bloc-content>
                <div class="space-y-3">
                    <div class="dark:text-white text-xl">Départ :</div>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroport ou gares"/>
                        <x-radio wire:model="backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                        <x-radio wire:model="backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="Créer une nouvelle adresse"/>
                    </div>
                    @if($backPickupMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_from_place"
                            label="Lieu"
                            placeholder="Sélectionner un lieu existant"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="reservation_back.localisation_from_id"
                        />
                        @if($reservation_back->localisation_from_id)
                            <div class="form-group">
                                <x-input label="Provenance / N°" wire:model="reservation_back.pickup_origin"/>
                            </div>
                        @endif
                    @endif
                    @if($backPickupMode == \App\Services\ReservationService::WITH_ADRESSE)
                        <x-select
                            wire:key="back_from_adresse"
                            label="Adresse"
                            placeholder="Sélectionner une adresse"
                            :async-data="route('api.adresses', ['user' => $userId])"
                            option-label="full_adresse"
                            option-value="id"
                            wire:model="reservation_back.adresse_reservation_from_id"
                        />
                    @endif
                    @if($backPickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                        <div class="space-y-4">
                            <x-input wire:model.defer="newAdresseReservationFromBack.adresse" label="Adresse"/>
                            <x-input wire:model.defer="newAdresseReservationFromBack.adresse_complement"
                                     label="Adresse complémentaire"/>
                            <x-input wire:model.defer="newAdresseReservationFromBack.code_postal" label="Code postal"/>
                            <x-input wire:model.defer="newAdresseReservationFromBack.ville" label="Ville"/>
                        </div>
                    @endif
                </div>
            </x-bloc-content>

            <x-bloc-content>
                <div class="space-y-3">
                    <div class="dark:text-white text-xl">Arrivée :</div>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroport ou gares"/>
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="Créer une nouvelle adresse"/>
                    </div>
                    @if($backDropMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_to_place"
                            label="Aéroport ou gares"
                            placeholder="Sélectionnez une gare ou un aéroport"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="reservation_back.localisation_to_id"
                        />
                        @if($reservation_back->localisation_to_id)
                            <div class="form-group">
                                <x-input label="Provenance / N°" wire:model="reservation_back.drop_off_origin"/>
                            </div>
                        @endif
                    @endif
                    @if($backDropMode == \App\Services\ReservationService::WITH_ADRESSE)
                        <x-select
                            wire:key="back_to_adresse"
                            label="Adresse"
                            placeholder="Sélectionner une adresse"
                            :async-data="route('api.adresses', ['user' => $userId])"
                            option-label="full_adresse"
                            option-value="id"
                            wire:model.defer="reservation_back.adresse_reservation_to_id"
                        />
                    @endif
                    @if($backDropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                        <div class="space-y-4">
                            <x-input label="Adresse" wire:model.defer="newAdresseReservationToBack.adresse"/>
                            <x-input label="Adresse complémentaire"
                                     wire:model.defer="newAdresseReservationToBack.adresse_complement"/>
                            <x-input label="Code postal" wire:model.defer="newAdresseReservationToBack.code_postal"/>
                            <x-input label="Ville" wire:model.defer="newAdresseReservationToBack.ville"/>
                        </div>
                    @endif
                </div>
            </x-bloc-content>

            <x-bloc-content>
                <div class="mb-4">
                    <x-textarea label="Commentaire" placeholder="Votre commentaire..."
                                wire:model="reservation_back.comment"/>
                </div>
            </x-bloc-content>
        @endif
        <x-bloc-content>
            <div class="flex flex-col space-y-2 my-3">
                <x-toggle wire:model="reservation.calendar_passager_invitation" md
                          label="{{ __('Envoyer une invitation Google Calendar au passager') }}"/>
                <x-toggle wire:model="reservation.send_to_passager" md
                          label="{!! __('Envoyer l\'email de création de la réservation au passager') !!}"/>
            </div>
        </x-bloc-content>
        <x-bloc-content>
            <x-button type="submit" primary label="Enregistrer" wire:loading.attr="disabled"/>
        </x-bloc-content>
    </form>
</div>

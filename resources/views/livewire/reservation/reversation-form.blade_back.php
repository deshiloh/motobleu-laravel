<div>
    <x-header>
        Formulaire de réservation {{ $reservation->reference ?? '' }}
    </x-header>
    <div class="container mx-auto sm:px-6 lg:px-8">
        <x-errors class="mb-3"/>
    </div>
    <form wire:submit="saveReservation" wire:loading.class="opacity-25" wire:key="form_reservation">
        @if(!$reservation->exists)
            <x-bloc-content>
                <div class="flex flex-col space-y-3">
                    <div class="dark:text-white block">
                        Réservation avec retour :
                    </div>
                    <div>
                        <x-toggle wire:model.live="form.hasBack" left-label="Non" label="Oui" md/>
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
                    option-description="email"
                    wire:model="form.userId"
                    x-on:selected="$wire.userSelected()"
                />
                <x-select
                    label="Entreprise rattachée *"
                    placeholder="Sélectionner une entreprise"
                    :async-data="route('api.entreprises_users', ['userId' => $form->userId])"
                    option-label="nom"
                    option-value="id"
                    wire:key="entreprise-{{ $form->userId }}"
                    wire:model="form.entreprise_id"
                />
                @if(!is_null($form->entreprise_id) && !in_array($form->entreprise_id, app(\app\Settings\BillSettings::class)->entreprise_without_command_field))
                    <x-input label="Numéro De commande / Case code" class="mb-3" wire:model.defer="form.commande"/>
                @endif
            </div>
        </x-bloc-content>
        <x-bloc-content>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">
                    Passager :
                </div>

                <div class="flex space-x-3">
                    <x-radio wire:model="form.passagerMode"
                             value="{{ \App\Services\ReservationService::EXIST_PASSAGER }}" label="Passager existant"/>
                    <x-radio wire:model="form.passagerMode"
                             value="{{ \App\Services\ReservationService::NEW_PASSAGER }}"
                             label="Créer un nouveau passager"/>
                </div>

                @if($form->passagerMode == \App\Services\ReservationService::EXIST_PASSAGER)
                    <x-select
                        wire:key="passenger-{{ $form->userId }}"
                        label="Passager existant"
                        placeholder="Sélectionner un passager"
                        :async-data="route('api.passagers', ['user' => $form->userId])"
                        option-label="nom"
                        option-value="id"
                        option-description="email"
                        wire:model="form.passager_id"
                    />
                @endif

                @if($form->passagerMode == \App\Services\ReservationService::NEW_PASSAGER)
                    <div class="space-y-4">
                        <x-input label="Nom et prénom" wire:model="form.newPassager.nom"/>
                        <x-input label="Téléphone de bureau" wire:model="form.newPassager.telephone"/>
                        <x-input label="Téléphone portable" wire:model="form.newPassager.portable"/>
                        <x-input type="email" label="Adresse email" wire:model="form.newPassager.email"/>
                        @if(!is_null($form->entreprise_id) && in_array($form->entreprise_id, app(\app\Settings\BillSettings::class)->entreprises_cost_center_facturation))
                            <x-select
                                wire:key="cost_center"
                                label="Cost Center"
                                placeholder="Sélectionner un Cost Center"
                                :async-data="route('api.cost_center')"
                                option-label="nom"
                                option-value="id"
                                wire:model="form.newPassager.cost_center_id"
                            />
                            <x-select
                                wire:key="type_facturation"
                                label="Type de facturation"
                                placeholder="Sélectionner un type de facturation"
                                :async-data="route('api.type_facturation')"
                                option-label="nom"
                                option-value="id"
                                wire:model="form.newPassager.type_facturation_id"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </x-bloc-content>

        <div class="md:px-4">
            <div class="relative mb-4">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-gray-100 px-3 font-semibold text-gray-900 text-2xl">Course aller</span>
                </div>
            </div>
        </div>

        <x-bloc-content>
            <x-datetime-picker
                wire:key="pickup_date"
                label="Date de prise en charge"
                placeholder="Choisir une date"
                display-format="DD/MM/YYYY HH:mm"
                time-format="24"
                interval="1"
                wire:model="form.pickup_date"
                :without-timezone="true"
            />
        </x-bloc-content>

        <x-bloc-content>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">Lieu de prise en charge :</div>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="form.pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroports ou gares"/>
                    <x-radio wire:model="form.pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                    <x-radio wire:model="form.pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                             label="Créer une nouvelle adresse"/>
                </div>

                @if($form->pickupMode == \App\Services\ReservationService::WITH_PLACE)
                    <div class="space-y-4">
                        <x-select
                            wire:key="from_place"
                            label="Aéroports ou gares"
                            placeholder="Sélectionnez une gare ou un aéroport"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.localisation_from_id"
                        />
                        @if($form->localisation_from_id)
                            <x-input label="Provenance / N°" wire:model="form.pickup_origin" />
                        @endif
                    </div>
                @endif

                @if($form->pickupMode == \App\Services\ReservationService::WITH_ADRESSE)
                    @if($reservation->exists())
                        <div class="border border-gray-300 p-2 rounded">
                            <span class="bold text-xl block">Adresse actuelle :</span>
                            <span>
                                {{ $reservation->display_from }}
                            </span>
                        </div>
                    @endif
                    <x-select
                        wire:key="from_adresse-{{ $form->userId }}"
                        label="Adresse"
                        placeholder="Sélectionner une adresse"
                        :async-data="route('api.adresses', ['user' => $form->userId])"
                        option-label="full_adresse"
                        option-value="id"
                        wire:model="form.addressReservationFrom"
                    />
                @endif

                @if($form->pickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                    <div class="space-y-4">
                        <x-input label="Adresse" wire:model.defer="form.newAdresseReservationFrom.adresse"/>
                        <x-input label="Adresse complémentaire"
                                 wire:model.defer="form.newAdresseReservationFrom.adresse_complement"/>
                        <x-input label="Code postal" wire:model.defer="form.newAdresseReservationFrom.code_postal"/>
                        <x-input label="Ville" wire:model.defer="form.newAdresseReservationFrom.ville"/>
                    </div>
                @endif
            </div>
        </x-bloc-content>

        <x-bloc-content>
            @include('components.reservation-form.other-steps')
        </x-bloc-content>

        <x-bloc-content>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">Lieu de destination :</div>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="form.dropMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroports ou gares"/>
                    <x-radio wire:model="form.dropMode"
                             value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                    <x-radio wire:model="form.dropMode"
                             value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                             label="Créer une nouvelle adresse"/>
                </div>

                @if($form->dropMode == \App\Services\ReservationService::WITH_PLACE)
                    <div class="space-y-4">
                        <x-select
                            wire:key="to_place"
                            label="Aéroports ou gares"
                            placeholder="Aéroports ou gares"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.localisation_to_id"
                        />
                        @if($form->localisation_to_id)
                            <div class="form-group">
                                <x-input label="Destination / N°" wire:model="form.drop_off_origin"/>
                            </div>
                        @endif
                    </div>
                @endif

                @if($form->dropMode == \App\Services\ReservationService::WITH_ADRESSE)
                    @if($reservation->exists())
                        <div class="border border-gray-300 p-2 rounded">
                            <span class="bold text-xl block">Adresse actuelle :</span>
                            <span>
                                {{ $reservation->display_to }}
                            </span>
                        </div>
                    @endif

                    <x-select
                        wire:key="to_adresse-{{ $form->userId }}"
                        label="Adresse"
                        placeholder="Sélectionner une adresse"
                        :async-data="route('api.adresses', ['user' => $form->userId])"
                        option-label="full_adresse"
                        option-value="id"
                        wire:model="form.addressReservationTo"
                    />
                @endif

                @if($form->dropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                    <div class="space-y-4">
                        <x-input label="Adresse" wire:model.defer="form.newAdresseReservationTo.adresse"/>
                        <x-input label="Adresse complémentaire"
                                 wire:model.defer="form.newAdresseReservationTo.adresse_complement"/>
                        <x-input label="Code postal" wire:model.defer="form.newAdresseReservationTo.code_postal"/>
                        <x-input label="Ville" wire:model.defer="form.newAdresseReservationTo.ville"/>
                    </div>
                @endif
            </div>
        </x-bloc-content>

        <x-bloc-content>
            <x-textarea placeholder="Votre commentaire..." wire:model.defer="form.comment" label="Commentaire" />
        </x-bloc-content>

        @if($form->hasBack)

            <div class="md:px-4">
                <div class="relative mb-4">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-gray-100 px-3 font-semibold text-gray-900 text-2xl">Course retour</span>
                    </div>
                </div>
            </div>

            <x-bloc-content-dark>
                <x-datetime-picker
                    wire:key="back_date_picker"
                    label="Date"
                    placeholder="Choisir une date"
                    display-format="DD/MM/YYYY HH:mm"
                    time-format="24"
                    interval="1"
                    wire:model="form.reservation_back.pickup_date"
                    :without-timezone="true"
                />
            </x-bloc-content-dark>

            <x-bloc-content-dark>
                <div class="space-y-3">
                    <div class="dark:text-white text-xl">Lieu de prise en charge :</div>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="form.backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroport ou gares"/>
                        <x-radio wire:model="form.backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                        <x-radio wire:model="form.backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="Créer une nouvelle adresse"/>
                    </div>
                    @if($form->backPickupMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_from_place"
                            label="Lieu"
                            placeholder="Sélectionner un lieu existant"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.reservation_back.localisation_from_id"
                        />
                        @if($reservation_back->localisation_from_id)
                            <div class="form-group">
                                <x-input label="Provenance / N°" wire:model="form.reservation_back.pickup_origin"/>
                            </div>
                        @endif
                    @endif
                    @if($form->backPickupMode == \App\Services\ReservationService::WITH_ADRESSE)
                        <x-select
                            wire:key="back_from_adresse-{{ $userId }}"
                            label="Adresse"
                            placeholder="Sélectionner une adresse"
                            :async-data="route('api.adresses', ['user' => $form->userId])"
                            option-label="full_adresse"
                            option-value="id"
                            wire:model="form.reservation_back.adresse_reservation_from_id"
                        />
                    @endif
                    @if($form->backPickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                        <div class="space-y-4">
                            <x-input wire:model.defer="form.newAdresseReservationFromBack.adresse" label="Adresse"/>
                            <x-input wire:model.defer="form.newAdresseReservationFromBack.adresse_complement"
                                     label="Adresse complémentaire"/>
                            <x-input wire:model.defer="form.newAdresseReservationFromBack.code_postal" label="Code postal"/>
                            <x-input wire:model.defer="form.newAdresseReservationFromBack.ville" label="Ville"/>
                        </div>
                    @endif
                </div>
            </x-bloc-content-dark>

            <x-bloc-content-dark>
                @include('components.reservation-form.back-other-steps')
            </x-bloc-content-dark>

            <x-bloc-content-dark>
                <div class="space-y-3">
                    <div class="dark:text-white text-xl">Lieu de destination :</div>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="form.backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroport ou gares"/>
                        <x-radio wire:model="form.backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                        <x-radio wire:model="form.backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="Créer une nouvelle adresse"/>
                    </div>
                    @if($form->backDropMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_to_place"
                            label="Aéroport ou gares"
                            placeholder="Sélectionnez une gare ou un aéroport"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.reservation_back.localisation_to_id"
                        />
                        @if($reservation_back->localisation_to_id)
                            <div class="form-group">
                                <x-input label="Destination / N°" wire:model="form.reservation_back.drop_off_origin"/>
                            </div>
                        @endif
                    @endif
                    @if($form->backDropMode == \App\Services\ReservationService::WITH_ADRESSE)
                        <x-select
                            wire:key="back_to_adresse-{{ $userId }}"
                            label="Adresse"
                            placeholder="Sélectionner une adresse"
                            :async-data="route('api.adresses', ['user' => $form->userId])"
                            option-label="full_adresse"
                            option-value="id"
                            wire:model.defer="form.reservation_back.adresse_reservation_to_id"
                        />
                    @endif
                    @if($form->backDropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                        <div class="space-y-4">
                            <x-input label="Adresse" wire:model.defer="form.newAdresseReservationToBack.adresse"/>
                            <x-input label="Adresse complémentaire"
                                     wire:model.defer="form.newAdresseReservationToBack.adresse_complement"/>
                            <x-input label="Code postal" wire:model.defer="form.newAdresseReservationToBack.code_postal"/>
                            <x-input label="Ville" wire:model.defer="form.newAdresseReservationToBack.ville"/>
                        </div>
                    @endif
                </div>
            </x-bloc-content-dark>

            <x-bloc-content-dark>
                <div class="mb-4">
                    <x-textarea label="Commentaire" placeholder="Votre commentaire..."
                                wire:model="form.reservation_back.comment"/>
                </div>
            </x-bloc-content-dark>
        @endif
        <x-bloc-content>
            <div class="flex flex-col space-y-2 my-3">
                <x-toggle wire:model="form.calendar_passager_invitation" md
                          label="{{ __('Envoyer une invitation Google Calendar au passager') }}"/>
                <x-toggle wire:model="form.send_to_passager" md
                          label="{!! __('Envoyer l\'email de création de la réservation au passager') !!}"/>
            </div>
        </x-bloc-content>
        <x-bloc-content>
            <x-button type="submit" primary label="Enregistrer" wire:loading.attr="disabled"/>
        </x-bloc-content>
    </form>

    <x-modal blur wire:model.defer="form.ardianPassengerCostFacError">
        <x-card title="Édition du passanger">
            @if($form->passengerInError)
                <form id="test" wire:submit.prevent="savePassenger" method="post">
                    <div class="space-y-3">
                        <div>
                            Passager : {{ $form->passengerInError->nom }}
                        </div>
                        <x-select
                            wire:key="cost_center_exist_passenger"
                            label="Cost Center"
                            placeholder="Sélectionner un Cost Center"
                            :async-data="route('api.cost_center')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.passengerInError.cost_center_id"
                        />
                        <x-select
                            wire:key="type_facturation_exist_passenger"
                            label="Type de facturation"
                            placeholder="Sélectionner un type de facturation"
                            :async-data="route('api.type_facturation')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.passengerInError.type_facturation_id"
                        />
                    </div>
                </form>
            @endif
            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button flat label="Annuler" x-on:click="close" />
                    <x-button primary label="Enregistrer" type="submit" form="test"/>
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>

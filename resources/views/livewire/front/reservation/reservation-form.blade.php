<div>
    <x-front.card>

        <x-front.title>
            {{ __('Formulaire de réservation') }}
            <x-slot:button>
                <x-button flat label="{{ __('Retour à la liste') }}" href="{{ route('front.reservation.list') }}"/>
            </x-slot:button>
        </x-front.title>

        <x-errors class="mt-3"/>
    </x-front.card>

    <form wire:submit.prevent="saveReservation" wire:loading.class="opacity-25" wire:key="form_reservation">
        <x-front.card>
            <div class="flex flex-col space-y-3">
                <div class="dark:text-white block">
                    {{ __('Réservation avec retour') }} :
                </div>
                <div>
                    <x-toggle wire:model="hasBack" left-label="{{ __('Non') }}" label="{{ __('Oui') }}" md/>
                </div>
            </div>
        </x-front.card>
        <x-front.card>
            <div class="space-y-3">
                @if(!in_array(Auth::user()->entreprises()->first()->id, app(\app\Settings\BillSettings::class)->entreprise_without_command_field))
                    <x-input label="{{ __('Numéro de commande / Case code') }}" wire:model="reservation.commande" />
                @endif

                <x-select
                    label="{{ __('Entreprise rattachée') }} *"
                    placeholder="{{ __('Sélectionner une entreprise') }}"
                    :async-data="route('api.entreprises_users', ['userId' => $userId])"
                    option-label="nom"
                    option-value="id"
                    wire:model="reservation.entreprise_id"
                />
            </div>

        </x-front.card>
        <x-front.card>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">
                    {{ __('Passager') }} :
                </div>

                <div class="flex space-x-3">
                    <x-radio wire:model="passagerMode"
                             value="{{ \App\Services\ReservationService::EXIST_PASSAGER }}" label="{{ __('Passager existant') }}"/>
                    <x-radio wire:model="passagerMode"
                             value="{{ \App\Services\ReservationService::NEW_PASSAGER }}"
                             label="{{ __('Créer un nouveau passager') }}"/>
                </div>

                @if($passagerMode == \App\Services\ReservationService::EXIST_PASSAGER)
                    <x-select
                        wire:key="passanger_choice"
                        label="{{ __('Passager existant') }}"
                        placeholder="{{ __('Sélectionner un passager') }}"
                        :async-data="route('api.passagers', ['user' => $userId])"
                        option-label="nom"
                        option-value="id"
                        option-description="email"
                        wire:model.defer="reservation.passager_id"
                    />
                @endif

                @if($passagerMode == \App\Services\ReservationService::NEW_PASSAGER)
                    <div class="space-y-4">
                        <x-input label="{{ __('Nom') }} {{ __('et') }} {{ __('prénom') }}" wire:model="newPassager.nom"/>
                        <x-input label="{{ __('Téléphone de bureau') }}" wire:model="newPassager.telephone"/>
                        <x-input label="{{ __('Téléphone portable') }}" wire:model="newPassager.portable"/>
                        <x-input type="email" label="{{ __('Adresse email') }}" wire:model="newPassager.email"/>
                        @if(in_array(Auth::user()->entreprises()->first()->id, app(\app\Settings\BillSettings::class)->entreprises_cost_center_facturation))
                            <x-select
                                wire:key="cost_center"
                                label="{{ __('Cost Center') }}"
                                placeholder="{{ __('Sélectionner un Cost Center') }}"
                                :async-data="route('api.cost_center')"
                                option-label="nom"
                                option-value="id"
                                wire:model="newPassager.cost_center_id"
                            />
                            <x-select
                                wire:key="type_facturation"
                                label="{{ __('Type de facturation') }}"
                                placeholder="{{ __('Sélectionner un type de facturation') }}"
                                :async-data="route('api.type_facturation')"
                                option-label="nom"
                                option-value="id"
                                wire:model="newPassager.type_facturation_id"
                            />
                        @endif
                    </div>
                @endif
            </div>
        </x-front.card>

        <div class="container mx-auto relative">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-gray-200 px-3 font-semibold text-gray-900 text-2xl">Course aller</span>
            </div>
        </div>

        <x-front.card>
            <x-datetime-picker
                wire:key="pickup_date"
                label="{{ __('Date de prise en charge') }}"
                placeholder="{{ __('Choisir une date') }}"
                display-format="DD/MM/YYYY HH:mm"
                time-format="24"
                interval="1"
                wire:model="reservation.pickup_date"
                :without-timezone="true"
                min="{{ \Carbon\Carbon::now() }}"
            />
        </x-front.card>
        <x-front.card>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">{{ __('Départ') }} :</div>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroports ou gares') }}"/>
                    <x-radio wire:model="pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
                    <x-radio wire:model="pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                             label="{{ __('Créer une nouvelle adresse') }}"/>
                </div>

                @if($pickupMode == \App\Services\ReservationService::WITH_PLACE)
                    <div class="space-y-4">
                        <x-select
                            wire:key="from_place"
                            label="{{ __('Aéroports ou gares') }}"
                            placeholder="{{ __('Sélectionnez une gare ou un aéroport') }}"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="reservation.localisation_from_id"
                        />
                        @if($reservation->localisation_from_id)
                            <x-input label="{{ __('Provenance / N°') }}" wire:model="reservation.pickup_origin" />
                        @endif
                    </div>
                @endif

                @if($pickupMode == \App\Services\ReservationService::WITH_ADRESSE)
                    <x-select
                        wire:key="from_adresse"
                        label="{{ __('Adresse') }}"
                        placeholder="{{ __('Sélectionner une adresse') }}"
                        :async-data="route('api.adresses', ['user' => Auth::user()->id])"
                        option-label="full_adresse"
                        option-value="id"
                        wire:model="reservation.adresse_reservation_from_id"
                    />
                @endif

                @if($pickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                    <div class="space-y-4">
                        <x-input label="{{ __('Adresse') }}" wire:model.defer="newAdresseReservationFrom.adresse"/>
                        <x-input label="{{ __('Adresse complémentaire') }}"
                                 wire:model.defer="newAdresseReservationFrom.adresse_complement"/>
                        <x-input label="{{ __('Code postal') }}" wire:model.defer="newAdresseReservationFrom.code_postal"/>
                        <x-input label="{{ __('Ville') }}" wire:model.defer="newAdresseReservationFrom.ville"/>
                    </div>
                @endif
            </div>
        </x-front.card>
        <x-front.card>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">{{ __('Arrivée') }} :</div>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="dropMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroports ou gares') }}"/>
                    <x-radio wire:model="dropMode"
                             value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
                    <x-radio wire:model="dropMode"
                             value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                             label="{{ __('Créer une nouvelle adresse') }}"/>
                </div>

                @if($dropMode == \App\Services\ReservationService::WITH_PLACE)
                    <div class="space-y-4">
                        <x-select
                            wire:key="to_place"
                            label="{{ __('Aéroports ou gares') }}"
                            placeholder="{{ __('Aéroports ou gares') }}"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="reservation.localisation_to_id"
                        />
                        @if($reservation->localisation_to_id)
                            <div class="form-group">
                                <x-input label="{{ __('Provenance / N°') }}" wire:model="reservation.drop_off_origin"/>
                            </div>
                        @endif
                    </div>
                @endif

                @if($dropMode == \App\Services\ReservationService::WITH_ADRESSE)
                    <x-select
                        wire:key="to_adresse"
                        label="{{ __('Adresse') }}"
                        placeholder="{{ __('Sélectionner une adresse') }}"
                        :async-data="route('api.adresses', ['user' => Auth::user()->id])"
                        option-label="full_adresse"
                        option-value="id"
                        wire:model="reservation.adresse_reservation_to_id"
                    />
                @endif

                @if($dropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                    <div class="space-y-4">
                        <x-input label="{{ __('Adresse') }}" wire:model.defer="newAdresseReservationTo.adresse"/>
                        <x-input label="{{ __('Adresse complémentaire') }}"
                                 wire:model.defer="newAdresseReservationTo.adresse_complement"/>
                        <x-input label="{{ __('Code postal') }}" wire:model.defer="newAdresseReservationTo.code_postal"/>
                        <x-input label="{{ __('Ville') }}" wire:model.defer="newAdresseReservationTo.ville"/>
                    </div>
                @endif
            </div>
        </x-front.card>
        <x-front.card>
            <x-textarea placeholder="{{ __('Votre commentaire') }}..." wire:model.defer="reservation.comment" label="Commentaire"/>
        </x-front.card>

        @if($hasBack)
            <div class="container mx-auto relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-gray-200 px-3 font-semibold text-gray-900 text-2xl">Course retour</span>
                </div>
            </div>
            <x-front.card>
                <x-datetime-picker
                    wire:key="back_date_picker"
                    label="{{ __('Date de retour') }}"
                    placeholder="{{ __('Choisir une date') }}"
                    display-format="DD/MM/YYYY HH:mm"
                    time-format="24"
                    interval="1"
                    wire:model="reservation_back.pickup_date"
                    :without-timezone="true"
                    min="{{ \Carbon\Carbon::now() }}"
                />
            </x-front.card>

            <x-front.card>

                <div class="space-y-3">
                    <div class="dark:text-white text-xl">{{ __('Départ du retour') }} :</div>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroport ou gares') }}"/>
                        <x-radio wire:model="backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
                        <x-radio wire:model="backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="{{ __('Créer une nouvelle adresse') }}"/>
                    </div>
                    @if($backPickupMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_from_place"
                            label="{{ __('Aéroports ou gares') }}"
                            placeholder="{{ __('Sélectionnez une gare ou un aéroport') }}"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="reservation_back.localisation_from_id"
                        />
                        @if($reservation_back->localisation_from_id)
                            <div class="form-group">
                                <x-input label="{{ __('Provenance / N°') }}" wire:model="reservation_back.pickup_origin"/>
                            </div>
                        @endif
                    @endif
                    @if($backPickupMode == \App\Services\ReservationService::WITH_ADRESSE)
                        <x-select
                            wire:key="back_from_adresse"
                            label="{{ __('Adresse') }}"
                            placeholder="{{ __('Sélectionner une adresse') }}"
                            :async-data="route('api.adresses', ['user' => Auth::user()->id])"
                            option-label="full_adresse"
                            option-value="id"
                            wire:model="reservation_back.adresse_reservation_from_id"
                        />
                    @endif
                    @if($backPickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                        <div class="space-y-4">
                            <x-input wire:model.defer="newAdresseReservationFromBack.adresse" label="{{ __('Adresse') }}"/>
                            <x-input wire:model.defer="newAdresseReservationFromBack.adresse_complement"
                                     label="{{ __('Adresse complémentaire') }}"/>
                            <x-input wire:model.defer="newAdresseReservationFromBack.code_postal" label="{{ __('Code postal') }}"/>
                            <x-input wire:model.defer="newAdresseReservationFromBack.ville" label="{{ __('Ville') }}"/>
                        </div>
                    @endif
                </div>
            </x-front.card>

            <x-front.card>

                <div class="space-y-3">
                    <div class="dark:text-white text-xl">{{ __('Arrivée du retour') }} :</div>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroport ou gares') }}"/>
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="{{ __('Créer une nouvelle adresse') }}"/>
                    </div>
                    @if($backDropMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_to_place"
                            label="{{ __('Aéroport ou gares') }}"
                            placeholder="{{ __('Sélectionnez une gare ou un aéroport') }}"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="reservation_back.localisation_to_id"
                        />
                        @if($reservation_back->localisation_to_id)
                            <div class="form-group">
                                <x-input label="{{ __('Provenance / N°') }}" wire:model="reservation_back.drop_off_origin"/>
                            </div>
                        @endif
                    @endif
                    @if($backDropMode == \App\Services\ReservationService::WITH_ADRESSE)
                        <x-select
                            wire:key="back_to_adresse"
                            label="{{ __('Adresse') }}"
                            placeholder="{{ __('Sélectionner une adresse') }}"
                            :async-data="route('api.adresses', ['user' => Auth::user()->id])"
                            option-label="full_adresse"
                            option-value="id"
                            wire:model.defer="reservation_back.adresse_reservation_to_id"
                        />
                    @endif
                    @if($backDropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                        <div class="space-y-4">
                            <x-input label="{{ __('Adresse') }}" wire:model.defer="newAdresseReservationToBack.adresse"/>
                            <x-input label="{{ __('Adresse complémentaire') }}"
                                     wire:model.defer="newAdresseReservationToBack.adresse_complement"/>
                            <x-input label="{{ __('Code postal') }}" wire:model.defer="newAdresseReservationToBack.code_postal"/>
                            <x-input label="{{ __('Ville') }}" wire:model.defer="newAdresseReservationToBack.ville"/>
                        </div>
                    @endif
                </div>
            </x-front.card>

            <x-front.card>

                <div class="mb-4">
                    <x-textarea label="{{ __('Commentaire du retour') }}" placeholder="{{ __('Votre commentaire') }}..."
                                wire:model="reservation_back.comment"/>
                </div>
            </x-front.card>
        @endif
        <x-front.card>

            <div class="flex flex-col space-y-2 my-3">
                <x-toggle wire:model="reservation.calendar_passager_invitation" md
                          label="{{ __('Envoyer une invitation Google Calendar au passager') }}"/>
                <x-toggle wire:model="reservation.send_to_passager" md
                          label="{!! __('Envoyer l\'email de création de la réservation au passager') !!}"/>
            </div>
        </x-front.card>
        <x-front.card>
            <x-button type="submit" primary label="{{ __('Enregistrer') }}" wire:loading.attr="disabled" spinner="saveReservation"/>
        </x-front.card>
    </form>
</div>

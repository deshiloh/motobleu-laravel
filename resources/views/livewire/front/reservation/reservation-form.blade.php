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
                    <x-toggle wire:model.live="form.hasBack" left-label="{{ __('Non') }}" label="{{ __('Oui') }}" md/>
                </div>
            </div>
        </x-front.card>
        <x-front.card>
            <div class="space-y-3">
                @if(Auth::user()->entreprises()->first() && !in_array(Auth::user()->entreprises()->first()->id, app(\app\Settings\BillSettings::class)->entreprise_without_command_field))
                    <x-input label="{{ __('Numéro de commande / Case code') }}" wire:model="form.commande" />
                @endif

                <x-select
                    label="{{ __('Entreprise rattachée') }} *"
                    placeholder="{{ __('Sélectionner une entreprise') }}"
                    :async-data="route('api.entreprises_users', ['userId' => $form->userId])"
                    option-label="nom"
                    option-value="id"
                    wire:model="form.entreprise_id"
                />
            </div>

        </x-front.card>
        <x-front.card>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">
                    {{ __('Passager') }} :
                </div>

                <div class="flex space-x-3">
                    <x-radio wire:model="form.passagerMode"
                             value="{{ \App\Services\ReservationService::EXIST_PASSAGER }}" label="{{ __('Passager existant') }}"/>
                    <x-radio wire:model="form.passagerMode"
                             value="{{ \App\Services\ReservationService::NEW_PASSAGER }}"
                             label="{{ __('Créer un nouveau passager') }}"/>
                </div>

                @if($form->passagerMode == \App\Services\ReservationService::EXIST_PASSAGER)
                    <x-select
                        wire:key="passanger_choice"
                        label="{{ __('Passager existant') }}"
                        placeholder="{{ __('Sélectionner un passager') }}"
                        :async-data="route('api.passagers', ['user' => $form->userId])"
                        option-label="nom"
                        option-value="id"
                        option-description="email"
                        wire:model="form.passager_id"
                    />
                @endif

                @if($form->passagerMode == \App\Services\ReservationService::NEW_PASSAGER)
                    <div class="space-y-4">
                        <x-input label="{{ __('Nom') }} {{ __('et') }} {{ __('prénom') }}" wire:model="form.newPassager.nom"/>
                        <x-input label="{{ __('Téléphone de bureau') }}" wire:model="form.newPassager.telephone"/>
                        <x-input label="{{ __('Téléphone portable') }}" wire:model="form.newPassager.portable"/>
                        <x-input type="email" label="{{ __('Adresse email') }}" wire:model="form.newPassager.email"/>
                        @if(Auth::user()->entreprises()->first() && in_array(Auth::user()->entreprises()->first()->id, app(\app\Settings\BillSettings::class)->entreprises_cost_center_facturation))
                            <x-native-select
                                wire:key="cost_center"
                                label="{{ __('Cost Center') }}"
                                placeholder="{{ __('Sélectionner un Cost Center') }}"
                                :options="
                                \App\Models\CostCenter::orderBy('nom')->where('is_actif', 1)->get(['id', 'nom'])->toArray()
                                "
                                option-label="nom"
                                option-value="id"
                                wire:model="form.newPassager.cost_center_id"
                            />
                            <x-select
                                wire:key="type_facturation"
                                label="{{ __('Type de facturation') }}"
                                placeholder="{{ __('Sélectionner un type de facturation') }}"
                                :async-data="route('api.type_facturation')"
                                option-label="nom"
                                option-value="id"
                                wire:model="form.newPassager.type_facturation_id"
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
                <span class="bg-gray-200 px-3 font-semibold text-gray-900 text-2xl">{{ __('Course aller') }}</span>
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
                wire:model="form.pickup_date"
                :without-timezone="true"
                min="{{ \Carbon\Carbon::now()->addMinutes(15) }}"
            />
        </x-front.card>

        <x-front.card>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">{{ __('Lieu de prise en charge') }} :</div>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="form.pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroports ou gares') }}"/>
                    <x-radio wire:model="form.pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
                    <x-radio wire:model="form.pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                             label="{{ __('Créer une nouvelle adresse') }}"/>
                </div>

                @if($form->pickupMode == \App\Services\ReservationService::WITH_PLACE)
                    <div class="space-y-4">
                        <x-select
                            wire:key="from_place"
                            label="{{ __('Aéroports ou gares') }}"
                            placeholder="{{ __('Sélectionnez une gare ou un aéroport') }}"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.localisation_from_id"
                        />
                        @if($form->localisation_from_id)
                            <x-input label="{{ __('Provenance / N°') }}" wire:model="form.pickup_origin" />
                        @endif
                    </div>
                @endif

                @if($form->pickupMode == \App\Services\ReservationService::WITH_ADRESSE)
                    <x-select
                        wire:key="from_adresse"
                        label="{{ __('Adresse') }}"
                        placeholder="{{ __('Sélectionner une adresse') }}"
                        :async-data="route('api.adresses', ['user' => Auth::user()->id])"
                        option-label="full_adresse"
                        option-value="id"
                        wire:model="form.addressReservationFrom"
                    />
                @endif

                @if($form->pickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                    <div class="space-y-4">
                        <x-input label="{{ __('Adresse') }}" wire:model.defer="form.newAdresseReservationFrom.adresse"/>
                        <x-input label="{{ __('Adresse complémentaire') }}"
                                 wire:model.defer="form.newAdresseReservationFrom.adresse_complement"/>
                        <x-input label="{{ __('Code postal') }}" wire:model.defer="form.newAdresseReservationFrom.code_postal"/>
                        <x-input label="{{ __('Ville') }}" wire:model.defer="form.newAdresseReservationFrom.ville"/>
                    </div>
                @endif
            </div>
        </x-front.card>

        <x-front.card>
            @include('components.reservation-form.other-steps')
        </x-front.card>

        <x-front.card>
            <div class="space-y-3">
                <div class="dark:text-white text-xl">{{ __('Lieu de destination') }} :</div>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="form.dropMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroports ou gares') }}"/>
                    <x-radio wire:model="form.dropMode"
                             value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
                    <x-radio wire:model="form.dropMode"
                             value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                             label="{{ __('Créer une nouvelle adresse') }}"/>
                </div>

                @if($form->dropMode == \App\Services\ReservationService::WITH_PLACE)
                    <div class="space-y-4">
                        <x-select
                            wire:key="to_place"
                            label="{{ __('Aéroports ou gares') }}"
                            placeholder="{{ __('Aéroports ou gares') }}"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.localisation_to_id"
                        />
                        @if($form->localisation_to_id)
                            <div class="form-group">
                                <x-input label="{{ __('Destination / N°') }}" wire:model="form.drop_off_origin"/>
                            </div>
                        @endif
                    </div>
                @endif

                @if($form->dropMode == \App\Services\ReservationService::WITH_ADRESSE)
                    <x-select
                        wire:key="to_adresse"
                        label="{{ __('Adresse') }}"
                        placeholder="{{ __('Sélectionner une adresse') }}"
                        :async-data="route('api.adresses', ['user' => Auth::user()->id])"
                        option-label="full_adresse"
                        option-value="id"
                        wire:model="form.addressReservationTo"
                    />
                @endif

                @if($form->dropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                    <div class="space-y-4">
                        <x-input label="{{ __('Adresse') }}" wire:model.defer="form.newAdresseReservationTo.adresse"/>
                        <x-input label="{{ __('Adresse complémentaire') }}"
                                 wire:model.defer="form.newAdresseReservationTo.adresse_complement"/>
                        <x-input label="{{ __('Code postal') }}" wire:model.defer="form.newAdresseReservationTo.code_postal"/>
                        <x-input label="{{ __('Ville') }}" wire:model.defer="form.newAdresseReservationTo.ville"/>
                    </div>
                @endif
            </div>
        </x-front.card>

        <x-front.card>
            <x-textarea placeholder="{{ __('Votre commentaire') }}..." wire:model.defer="form.comment" label="{{ __('Commentaire') }}"/>
        </x-front.card>

        @if($form->hasBack)
            <div class="container mx-auto relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-gray-200 px-3 font-semibold text-gray-900 text-2xl">{{ __('Course retour') }}</span>
                </div>
            </div>
            <x-front.card-dark>
                <x-datetime-picker
                    wire:key="back_date_picker"
                    label="{{ __('Date de retour') }}"
                    placeholder="{{ __('Choisir une date') }}"
                    display-format="DD/MM/YYYY HH:mm"
                    time-format="24"
                    interval="1"
                    wire:model="form.reservation_back.pickup_date"
                    :without-timezone="true"
                    min="{{ \Carbon\Carbon::now() }}"
                />
            </x-front.card-dark>

            <x-front.card-dark>

                <div class="space-y-3">
                    <div class="dark:text-white text-xl">{{ __('Lieu de prise en charge') }} :</div>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="form.backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroport ou gares') }}"/>
                        <x-radio wire:model="form.backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
                        <x-radio wire:model="form.backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="{{ __('Créer une nouvelle adresse') }}"/>
                    </div>
                    @if($form->backPickupMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_from_place"
                            label="{{ __('Aéroports ou gares') }}"
                            placeholder="{{ __('Sélectionnez une gare ou un aéroport') }}"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.reservation_back.localisation_from_id"
                        />
                        @if(isset($form->reservation_back['localisation_from_id']) && $form->reservation_back['localisation_from_id'])
                            <div class="form-group">
                                <x-input label="{{ __('Destination / N°') }}" wire:model="form.reservation_back.pickup_origin"/>
                            </div>
                        @endif
                    @endif
                    @if($form->backPickupMode == \App\Services\ReservationService::WITH_ADRESSE)
                        <x-select
                            wire:key="back_from_adresse"
                            label="{{ __('Adresse') }}"
                            placeholder="{{ __('Sélectionner une adresse') }}"
                            :async-data="route('api.adresses', ['user' => Auth::user()->id])"
                            option-label="full_adresse"
                            option-value="id"
                            wire:model="form.reservation_back.adresse_reservation_from_id"
                        />
                    @endif
                    @if($form->backPickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                        <div class="space-y-4">
                            <x-input wire:model.defer="form.newAdresseReservationFromBack.adresse" label="{{ __('Adresse') }}"/>
                            <x-input wire:model.defer="form.newAdresseReservationFromBack.adresse_complement"
                                     label="{{ __('Adresse complémentaire') }}"/>
                            <x-input wire:model.defer="form.newAdresseReservationFromBack.code_postal" label="{{ __('Code postal') }}"/>
                            <x-input wire:model.defer="form.newAdresseReservationFromBack.ville" label="{{ __('Ville') }}"/>
                        </div>
                    @endif
                </div>
            </x-front.card-dark>

        <x-front.card-dark>
            @include('components.reservation-form.back-other-steps')
        </x-front.card-dark>

            <x-front.card-dark>

                <div class="space-y-3">
                    <div class="dark:text-white text-xl">{{ __('Lieu de destination') }} :</div>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="form.backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroport ou gares') }}"/>
                        <x-radio wire:model="form.backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
                        <x-radio wire:model="form.backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="{{ __('Créer une nouvelle adresse') }}"/>
                    </div>
                    @if($form->backDropMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_to_place"
                            label="{{ __('Aéroport ou gares') }}"
                            placeholder="{{ __('Sélectionnez une gare ou un aéroport') }}"
                            :async-data="route('api.pickupplace')"
                            option-label="nom"
                            option-value="id"
                            wire:model="form.reservation_back.localisation_to_id"
                        />
                        @if(isset($form->reservation_back['localisation_to_id']) && $form->reservation_back['localisation_to_id'])
                            <div class="form-group">
                                <x-input label="{{ __('Destination / N°') }}" wire:model="form.reservation_back.drop_off_origin"/>
                            </div>
                        @endif
                    @endif
                    @if($form->backDropMode == \App\Services\ReservationService::WITH_ADRESSE)
                        <x-select
                            wire:key="back_to_adresse"
                            label="{{ __('Adresse') }}"
                            placeholder="{{ __('Sélectionner une adresse') }}"
                            :async-data="route('api.adresses', ['user' => Auth::user()->id])"
                            option-label="full_adresse"
                            option-value="id"
                            wire:model.defer="form.reservation_back.adresse_reservation_to_id"
                        />
                    @endif
                    @if($form->backDropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
                        <div class="space-y-4">
                            <x-input label="{{ __('Adresse') }}" wire:model.defer="form.newAdresseReservationToBack.adresse"/>
                            <x-input label="{{ __('Adresse complémentaire') }}"
                                     wire:model.defer="form.newAdresseReservationToBack.adresse_complement"/>
                            <x-input label="{{ __('Code postal') }}" wire:model.defer="form.newAdresseReservationToBack.code_postal"/>
                            <x-input label="{{ __('Ville') }}" wire:model.defer="form.newAdresseReservationToBack.ville"/>
                        </div>
                    @endif
                </div>
            </x-front.card-dark>

            <x-front.card-dark>

                <div class="mb-4">
                    <x-textarea label="{{ __('Commentaire course retour') }}" placeholder="{{ __('Votre commentaire') }}..."
                                wire:model="form.reservation_back.comment"/>
                </div>
            </x-front.card-dark>
        @endif
        <x-front.card>

            <div class="flex flex-col space-y-2 my-3">
                <x-toggle wire:model="form.calendar_passager_invitation" md
                          label="{{ __('Envoyer une invitation Google Calendar au passager') }}"/>
                <x-toggle wire:model="form.send_to_passager" md
                          label="{!! __('Envoyer l\'email de création de la réservation au passager') !!}"/>
            </div>
        </x-front.card>
        <x-front.card>
            <x-button type="submit" primary label="{{ __('Enregistrer') }}" wire:loading.attr="disabled" spinner="saveReservation"/>
        </x-front.card>
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

<div>
    <x-header>
        Formulaire de réservation
    </x-header>
    <x-errors class="mb-3"/>
    <x-bloc-content>
        <form wire:submit.prevent="saveReservation" wire:loading.class="opacity-25" wire:key="form_reservation">
            <x-input label="Numéro de commande" class="mb-3" wire:model.defer="reservation.commande"/>
            <div class="flex flex-col space-y-2 my-3">
                <x-toggle wire:model="reservation.send_to_passager" md
                          label="Envoyer par mail une invitation d’agenda au passager"/>
                <x-toggle wire:model="reservation.send_to_user" md
                          label="Envoyer par mail une invitation d’agenda à l’assistante"/>
            </div>
            <div class="mb-3">
                <x-select
                    label="Secrétaire"
                    placeholder="Sélectionner une secrétaire"
                    :async-data="route('api.users')"
                    option-label="full_name"
                    option-value="id"
                    option-description="entreprise.nom"
                    wire:model="userId"
                />
            </div>
            <fieldset>
                <legend>Passager :</legend>

                <div class="flex mb-3 space-x-3">
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
                        :async-data="route('admin.api.passagers', ['user' => $userId])"
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
                        @if(\Illuminate\Support\Facades\Auth::user()->entreprise->id == 1)
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
            </fieldset>

            <x-datetime-picker
                wire:key="pickup_date"
                label="Date de prise en charge"
                placeholder="Choisir une date"
                display-format="DD/MM/YYYY HH:mm"
                time-format="24"
                interval="5"
                wire:model="reservation.pickup_date"
                :without-timezone="true"
                min="{{ \Carbon\Carbon::now() }}"
            />

            <fieldset class="mt-3">
                <legend>Départ :</legend>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="pickupMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Lieu"/>
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
                            label="Lieu"
                            placeholder="Sélectionner un lieu existant"
                            :async-data="route('admin.api.pickupplace')"
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
                        :async-data="route('admin.api.adresses')"
                        option-label="adresse"
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
            </fieldset>
            <fieldset class="mt-3">
                <legend>Arrivée :</legend>

                <div class="flex mb-3 space-x-3">
                    <x-radio wire:model="dropMode"
                             value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Lieu"/>
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
                            label="Lieu"
                            placeholder="Sélectionner un lieu existant"
                            :async-data="route('admin.api.pickupplace')"
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
                        :async-data="route('admin.api.adresses')"
                        option-label="adresse"
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
            </fieldset>
            <x-textarea placeholder="Votre commentaire..." wire:model.defer="reservation.comment" label="Commentaire"/>
            <div class="flex my-3">
                <x-toggle wire:model="hasBack" label="Réserver le retour" md/>
            </div>
            @if($hasBack)
                <x-datetime-picker
                    wire:key="back_date_picker"
                    label="Date de retour"
                    placeholder="Choisir une date"
                    display-format="DD/MM/YYYY HH:mm"
                    time-format="24"
                    interval="5"
                    wire:model="reservation_back.pickup_date"
                    :without-timezone="true"
                    min="{{ \Carbon\Carbon::now() }}"
                />

                <fieldset class="mt-3">
                    <legend>Départ :</legend>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="backPickupMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Lieu"/>
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
                            :async-data="route('admin.api.pickupplace')"
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
                            :async-data="route('admin.api.adresses')"
                            option-label="adresse"
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
                </fieldset>
                <fieldset class="mt-3">
                    <legend>Arrivée :</legend>
                    <div class="flex mb-3 space-x-3">
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Lieu"/>
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
                        <x-radio wire:model="backDropMode"
                                 value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                                 label="Créer une nouvelle adresse"/>
                    </div>
                    @if($backDropMode == \App\Services\ReservationService::WITH_PLACE)
                        <x-select
                            wire:key="back_to_place"
                            label="Lieu"
                            placeholder="Sélectionner un lieu existant"
                            :async-data="route('admin.api.pickupplace')"
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
                            :async-data="route('admin.api.adresses')"
                            option-label="adresse"
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
                </fieldset>
                <div class="mb-4">
                    <x-textarea label="Commentaire" placeholder="Votre commentaire..."
                                wire:model="reservation_back.comment"/>
                </div>
            @endif
            <x-button type="submit" primary label="Enregistrer" wire:loading.attr="disabled"/>
        </form>
    </x-bloc-content>
</div>

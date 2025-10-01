<x-bloc-content-dark>
    <div class="space-y-3">
        <div class="dark:text-white text-xl">Lieu de prise en charge :</div>
        <div class="flex mb-3 space-x-3">
            <x-radio wire:model.live="form.backPickupMode"
                     value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroport ou gares"/>
            <x-radio wire:model.live="form.backPickupMode"
                     value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
            <x-radio wire:model.live="form.backPickupMode"
                     value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                     label="Créer une nouvelle adresse"/>
        </div>
        @if($form->backPickupMode == \App\Services\ReservationService::WITH_PLACE)
            <x-select
                id="back_from_place_select"
                wire:key="back_from_place"
                label="Lieu"
                placeholder="Sélectionner un lieu existant"
                :async-data="route('api.pickupplace')"
                option-label="nom"
                option-value="id"
                wire:model.live="form.reservationBack.localisationFromId"
            />
            @if(!empty($form->reservationBack['localisationFromId']))
                <div class="form-group">
                    <x-input label="Provenance / N°" wire:model="form.reservationBack.pickupOrigin"/>
                </div>
            @endif
        @endif
        @if($form->backPickupMode == \App\Services\ReservationService::WITH_ADRESSE)
            <x-select
                id="back_from_adresse_select"
                wire:key="back_from_adresse-{{ $form->userId }}"
                label="Adresse"
                placeholder="Sélectionner une adresse"
                :async-data="route('api.adresses', ['user' => $form->userId])"
                option-label="full_adresse"
                option-value="id"
                wire:model="form.reservationBack.adresseReservationFromId"
            />
        @endif
        @if($form->backPickupMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
            <div class="space-y-4">
                <x-input wire:model="form.newAdresseReservationFromBack.adresse" label="Adresse"/>
                <x-input wire:model="form.newAdresseReservationFromBack.adresseComplement"
                         label="Adresse complémentaire"/>
                <x-input wire:model="form.newAdresseReservationFromBack.codePostal" label="Code postal"/>
                <x-input wire:model="form.newAdresseReservationFromBack.ville" label="Ville"/>
            </div>
        @endif
    </div>
</x-bloc-content-dark>

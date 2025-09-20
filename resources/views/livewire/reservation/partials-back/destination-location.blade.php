<x-bloc-content-dark>
    <div class="space-y-3">
        <div class="dark:text-white text-xl">Lieu de destination :</div>
        <div class="flex mb-3 space-x-3">
            <x-radio wire:model.live="form.backDropMode"
                     value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroport ou gares"/>
            <x-radio wire:model.live="form.backDropMode"
                     value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
            <x-radio wire:model.live="form.backDropMode"
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
                wire:model="form.reservationBack.localisationToId"
            />
            @if(!empty($form->reservationBack['localisationToId']))
                <div class="form-group">
                    <x-input label="Destination / N°" wire:model="form.reservationBack.dropOffOrigin"/>
                </div>
            @endif
        @endif
        @if($form->backDropMode == \App\Services\ReservationService::WITH_ADRESSE)
            <x-select
                wire:key="back_to_adresse-{{ $form->userId }}"
                label="Adresse"
                placeholder="Sélectionner une adresse"
                :async-data="route('api.adresses', ['user' => $form->userId])"
                option-label="full_adresse"
                option-value="id"
                wire:model="form.reservationBack.adresseReservationToId"
            />
        @endif
        @if($form->backDropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
            <div class="space-y-4">
                <x-input label="Adresse" wire:model="form.newAdresseReservationToBack.adresse"/>
                <x-input label="Adresse complémentaire"
                         wire:model="form.newAdresseReservationToBack.adresseComplement"/>
                <x-input label="Code postal" wire:model="form.newAdresseReservationToBack.codePostal"/>
                <x-input label="Ville" wire:model="form.newAdresseReservationToBack.ville"/>
            </div>
        @endif
    </div>
</x-bloc-content-dark>
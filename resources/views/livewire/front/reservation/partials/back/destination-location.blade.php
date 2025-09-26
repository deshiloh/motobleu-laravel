<x-front.card-dark>
    <div class="space-y-3">
        <div class="dark:text-white text-xl">{{ __('Lieu de destination') }} :</div>
        <div class="flex mb-3 space-x-3">
            <x-radio wire:model.live="form.backDropMode"
                     value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroport ou gares') }}"/>
            <x-radio wire:model.live="form.backDropMode"
                     value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
            <x-radio wire:model.live="form.backDropMode"
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
                wire:model.live="form.reservationBack.localisationToId"
            />
            @if(!empty($form->reservationBack['localisationToId']))
                <div class="form-group">
                    <x-input label="{{ __('Destination / N°') }}" wire:model="form.reservationBack.dropOffOrigin"/>
                </div>
            @endif
        @endif
        @if($form->backDropMode == \App\Services\ReservationService::WITH_ADRESSE)
            <x-select
                wire:key="back_to_adresse-{{ $form->userId }}"
                label="{{ __('Adresse') }}"
                placeholder="{{ __('Sélectionner une adresse') }}"
                :async-data="route('api.adresses', ['user' => $form->userId])"
                option-label="full_adresse"
                option-value="id"
                wire:model="form.reservationBack.adresseReservationToId"
            />
        @endif
        @if($form->backDropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
            <div class="space-y-4">
                <x-input label="{{ __('Adresse') }}" wire:model="form.newAdresseReservationToBack.adresse"/>
                <x-input label="{{ __('Adresse complémentaire') }}"
                         wire:model="form.newAdresseReservationToBack.adresseComplement"/>
                <x-input label="{{ __('Code postal') }}" wire:model="form.newAdresseReservationToBack.codePostal"/>
                <x-input label="{{ __('Ville') }}" wire:model="form.newAdresseReservationToBack.ville"/>
            </div>
        @endif
    </div>
</x-front.card-dark>
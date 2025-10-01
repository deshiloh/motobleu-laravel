<x-bloc-content>
    <div class="space-y-3">
        <div class="dark:text-white text-xl">Lieu de prise en charge :</div>

        <div class="flex mb-3 space-x-3">
            <x-radio wire:model.live="form.pickupMode"
                     value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroports ou gares"/>
            <x-radio wire:model.live="form.pickupMode"
                     value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
            <x-radio wire:model.live="form.pickupMode"
                     value="{{ \App\Services\ReservationService::WITH_NEW_ADRESSE }}"
                     label="Créer une nouvelle adresse"/>
        </div>

        @if($form->pickupMode == \App\Services\ReservationService::WITH_PLACE)
            <div class="space-y-4">
                <x-select
                    id="from_place_select"
                    wire:key="from_place"
                    label="Aéroports ou gares"
                    placeholder="Sélectionnez une gare ou un aéroport"
                    :async-data="route('api.pickupplace')"
                    option-label="nom"
                    option-value="id"
                    wire:model.live="form.localisationFromId"
                />
                @if($form->localisationFromId)
                    <x-input label="Provenance / N°" wire:model="form.pickupOrigin" />
                @endif
            </div>
        @endif

        @if($form->pickupMode == \App\Services\ReservationService::WITH_ADRESSE)
            @if($reservation->exists)
                <div class="border border-gray-300 p-2 rounded">
                    <span class="bold text-xl block">Adresse actuelle :</span>
                    <span>
                        {{ $reservation->display_from }}
                    </span>
                </div>
            @endif
            <x-select
                id="from_adresse_select"
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
                <x-input label="Code postal" wire:model.defer="form.newAdresseReservationFrom.codePostal"/>
                <x-input label="Ville" wire:model.defer="form.newAdresseReservationFrom.ville"/>
            </div>
        @endif
    </div>
</x-bloc-content>
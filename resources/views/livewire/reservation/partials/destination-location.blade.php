<x-bloc-content>
    <div class="space-y-3">
        <div class="dark:text-white text-xl">Lieu de destination :</div>

        <div class="flex mb-3 space-x-3">
            <x-radio wire:model.live="form.dropMode"
                     value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="Aéroports ou gares"/>
            <x-radio wire:model.live="form.dropMode"
                     value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="Adresse"/>
            <x-radio wire:model.live="form.dropMode"
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
                    wire:model.live="form.localisationToId"
                />
                @if($form->localisationToId)
                    <div class="form-group">
                        <x-input label="Destination / N°" wire:model="form.dropOffOrigin"/>
                    </div>
                @endif
            </div>
        @endif

        @if($form->dropMode == \App\Services\ReservationService::WITH_ADRESSE)
            @if($reservation->exists)
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
                <x-input label="Adresse" wire:model="form.newAdresseReservationTo.adresse"/>
                <x-input label="Adresse complémentaire"
                         wire:model="form.newAdresseReservationTo.adresseComplement"/>
                <x-input label="Code postal" wire:model="form.newAdresseReservationTo.codePostal"/>
                <x-input label="Ville" wire:model="form.newAdresseReservationTo.ville"/>
            </div>
        @endif
    </div>
</x-bloc-content>

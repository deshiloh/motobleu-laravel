<x-front.card>
    <div class="space-y-3">
        <div class="dark:text-white text-xl">{{ __('Lieu de destination') }} :</div>

        <div class="flex mb-3 space-x-3">
            <x-radio wire:model.live="form.dropMode"
                     value="{{ \App\Services\ReservationService::WITH_PLACE }}" label="{{ __('Aéroports ou gares') }}"/>
            <x-radio wire:model.live="form.dropMode"
                     value="{{ \App\Services\ReservationService::WITH_ADRESSE }}" label="{{ __('Adresse') }}"/>
            <x-radio wire:model.live="form.dropMode"
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
                    wire:model.live="form.localisationToId"
                />
                @if($form->localisationToId)
                    <div class="form-group">
                        <x-input label="{{ __('Destination / N°') }}" wire:model="form.dropOffOrigin"/>
                    </div>
                @endif
            </div>
        @endif

        @if($form->dropMode == \App\Services\ReservationService::WITH_ADRESSE)
            <x-select
                wire:key="to_adresse-{{ $form->userId }}"
                label="{{ __('Adresse') }}"
                placeholder="{{ __('Sélectionner une adresse') }}"
                :async-data="route('api.adresses', ['user' => $form->userId])"
                option-label="full_adresse"
                option-value="id"
                wire:model="form.addressReservationTo"
            />
        @endif

        @if($form->dropMode == \App\Services\ReservationService::WITH_NEW_ADRESSE)
            <div class="space-y-4">
                <x-input label="{{ __('Adresse') }}" wire:model="form.newAdresseReservationTo.adresse"/>
                <x-input label="{{ __('Adresse complémentaire') }}"
                         wire:model="form.newAdresseReservationTo.adresseComplement"/>
                <x-input label="{{ __('Code postal') }}" wire:model="form.newAdresseReservationTo.codePostal"/>
                <x-input label="{{ __('Ville') }}" wire:model="form.newAdresseReservationTo.ville"/>
            </div>
        @endif
    </div>
</x-front.card>
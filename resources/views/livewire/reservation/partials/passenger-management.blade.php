<x-bloc-content>
    <div class="space-y-2">
        <div class="dark:text-white text-xl">
            Passager :
        </div>
        <div class="flex space-x-3">
            <x-radio wire:model.live="form.passengerMode"
                     value="{{ \App\Services\ReservationService::EXIST_PASSAGER }}" label="Passager existant"/>
            <x-radio wire:model.live="form.passengerMode"
                     value="{{ \App\Services\ReservationService::NEW_PASSAGER }}"
                     label="Créer un nouveau passager"/>
        </div>

        @if($form->passengerMode === \App\Services\ReservationService::EXIST_PASSAGER)
            <x-select
                id="passenger_select"
                wire:key="passenger-{{ $form->userId }}"
                label="Passager existant"
                placeholder="Sélectionner un passager"
                :async-data="route('api.passagers', ['user' => $form->userId])"
                option-label="nom"
                option-value="id"
                option-description="email"
                wire:model="form.passengerId"
            />
        @endif

        @if($form->passengerMode == \App\Services\ReservationService::NEW_PASSAGER)
            <div class="space-y-4">
                <x-input label="Nom et prénom" wire:model="form.newPassager.nom"/>
                <x-input label="Téléphone de bureau" wire:model="form.newPassager.telephone"/>
                <x-input label="Téléphone portable" wire:model="form.newPassager.portable"/>
                <x-input type="email" label="Adresse email" wire:model="form.newPassager.email"/>
                @if(!is_null($form->entrepriseId) && in_array($form->entrepriseId, app(\app\Settings\BillSettings::class)->entreprises_cost_center_facturation))
                    <x-select
                        id="cost_center_select"
                        wire:key="cost_center"
                        label="Cost Center"
                        placeholder="Sélectionner un Cost Center"
                        :async-data="route('api.cost_center')"
                        option-label="nom"
                        option-value="id"
                        wire:model="form.newPassager.cost_center_id"
                    />
                    <x-select
                        id="type_facturation_select"
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
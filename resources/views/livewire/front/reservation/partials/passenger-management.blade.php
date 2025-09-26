<x-front.card>
    <div class="space-y-2">
        <div class="dark:text-white text-xl">
            {{ __('Passager') }} :
        </div>
        <div class="flex space-x-3">
            <x-radio wire:model.live="form.passengerMode"
                     value="{{ \App\Services\ReservationService::EXIST_PASSAGER }}" label="{{ __('Passager existant') }}"/>
            <x-radio wire:model.live="form.passengerMode"
                     value="{{ \App\Services\ReservationService::NEW_PASSAGER }}"
                     label="{{ __('Créer un nouveau passager') }}"/>
        </div>

        @if($form->passengerMode === \App\Services\ReservationService::EXIST_PASSAGER)
            <x-select
                wire:key="passenger-{{ $form->userId }}"
                label="{{ __('Passager existant') }}"
                placeholder="{{ __('Sélectionner un passager') }}"
                :async-data="route('api.passagers', ['user' => $form->userId])"
                option-label="nom"
                option-value="id"
                option-description="email"
                wire:model="form.passengerId"
            />
        @endif

        @if($form->passengerMode == \App\Services\ReservationService::NEW_PASSAGER)
            <div class="space-y-4">
                <x-input label="{{ __('Nom et prénom') }}" wire:model="form.newPassager.nom"/>
                <x-input label="{{ __('Téléphone de bureau') }}" wire:model="form.newPassager.telephone"/>
                <x-input label="{{ __('Téléphone portable') }}" wire:model="form.newPassager.portable"/>
                <x-input type="email" label="{{ __('Adresse email') }}" wire:model="form.newPassager.email"/>
                @if(!is_null($form->entrepriseId) && in_array($form->entrepriseId, app(\app\Settings\BillSettings::class)->entreprises_cost_center_facturation))
                    <x-select
                        wire:key="cost_center"
                        label="{{ __('Cost Center') }}"
                        placeholder="{{ __('Sélectionner un Cost Center') }}"
                        :async-data="route('api.cost_center')"
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
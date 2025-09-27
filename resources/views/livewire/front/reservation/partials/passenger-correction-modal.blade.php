<x-modal blur wire:model.defer="form.ardianPassengerCostFacError" width="full">
    <x-card title="{{ __('Édition du passager') }}">
        @if($form->passengerInError)
            <form id="passenger_correction" wire:submit.prevent="savePassenger" method="post">
                <div class="space-y-3">
                    <div>
                        {{ __('Passager') }} : {{ $form->passengerInError->nom }}
                    </div>
                    <x-select
                        wire:key="cost_center_exist_passenger"
                        label="{{ __('Cost Center') }}"
                        placeholder="{{ __('Sélectionner un Cost Center') }}"
                        :async-data="route('api.cost_center')"
                        option-label="nom"
                        option-value="id"
                        wire:model="form.passengerInError.cost_center_id"
                    />
                    <x-select
                        wire:key="type_facturation_exist_passenger"
                        label="{{ __('Type de facturation') }}"
                        placeholder="{{ __('Sélectionner un type de facturation') }}"
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
                <x-button flat label="{{ __('Annuler') }}" x-on:click="close" />
                <x-button primary label="{{ __('Enregistrer') }}" type="submit" form="passenger_correction"/>
            </div>
        </x-slot>
    </x-card>
</x-modal>
<x-front.card>
    <div class="space-y-3">
        <x-select
            label="{{ __('Entreprise rattachée') }} *"
            placeholder="{{ __('Sélectionner une entreprise') }}"
            :async-data="route('api.entreprises_users', ['userId' => $form->userId])"
            option-label="nom"
            option-value="id"
            wire:key="entreprise-{{ $form->userId }}"
            wire:model.live="form.entrepriseId"
        />
        @if(!is_null($form->entrepriseId) && !in_array($form->entrepriseId, app(\app\Settings\BillSettings::class)->entreprise_without_command_field))
            <x-input
                wire:key="entreprise-{{$form->entrepriseId}}"
                label="{{ __('Numéro de commande / Case code') }}"
                wire:model="form.commande"
            />
        @endif
    </div>
</x-front.card>
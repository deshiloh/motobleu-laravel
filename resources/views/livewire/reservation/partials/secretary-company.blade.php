<x-bloc-content>
    <div class="space-y-3">
        <x-select
            label="Secrétaire *"
            placeholder="Sélectionner une secrétaire"
            :async-data="route('api.users')"
            option-label="full_name"
            option-value="id"
            option-description="email"
            wire:model.live="form.userId"
        />
        <x-select
            label="Entreprise rattachée *"
            placeholder="Sélectionner une entreprise"
            :async-data="route('api.entreprises_users', ['userId' => $form->userId])"
            option-label="nom"
            option-value="id"
            wire:key="entreprise-{{ $form->userId }}"
            wire:model.live="form.entrepriseId"
        />
        @if(!is_null($form->entrepriseId) && !in_array($form->entrepriseId, app(\app\Settings\BillSettings::class)->entreprise_without_command_field))
            <x-input wire:key="entreprise-{{$form->entrepriseId}}" label="Numéro De commande / Case code" class="mb-3" wire:model="form.commande"/>
        @endif
    </div>
</x-bloc-content>
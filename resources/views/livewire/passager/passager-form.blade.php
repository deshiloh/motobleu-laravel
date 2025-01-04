<div>
    <x-header>
        {!! $passager->exists ? "Modification du passager <span class='text-blue-500'>". $passager->nom ."</span>" : "Création d'un passager" !!}
    </x-header>
    <x-bloc-content>
        <form wire:submit="save" wire:loading.class="opacity-25" class="space-y-4">
            <x-input label="Nom & prénom" wire:model.live="passager.nom" />
            <x-input type="email" label="Adresse email" wire:model.live="passager.email" />
            <x-input type="tel" label="Téléphone bureau" wire:model.live="passager.telephone"/>
            <x-input label="Téléphone portable" wire:model.live="passager.portable" />
            <x-select
                label="Secrétaire"
                placeholder="Sélectionner une secrétaire"
                :async-data="route('api.users')"
                option-label="full_name"
                option-value="id"
                option-description="entreprise.nom"
                wire:model.live="passager.user_id"
            />
            <x-select
                label="Cost Center"
                placeholder="Sélectionner Cost Center"
                :async-data="route('api.cost_center')"
                option-label="nom"
                option-value="id"
                option-description="entreprise.nom"
                wire:model.live="passager.cost_center_id"
            />
            <x-select
                label="Type Facturation"
                placeholder="Sélectionner un type de facturation"
                :async-data="route('api.type_facturation')"
                option-label="nom"
                option-value="id"
                option-description="entreprise.nom"
                wire:model.live="passager.type_facturation_id"
            />
            <x-button type="submit" primary sm  label="Enregistrer"/>
        </form>
    </x-bloc-content>
</div>

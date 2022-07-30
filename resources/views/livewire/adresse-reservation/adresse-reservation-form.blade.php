<div>
    <x-header>
        {!! $adresseReservation->exists ? "Modification de l'adresse <span class='text-blue-500'>".$adresseReservation->adresse."</span>" : "Création d'une adresse" !!}
    </x-header>

    <x-bloc-content>
        <form wire:submit.prevent="save" class="space-y-4">
            <x-input label="Adresse" wire:model.defer="adresseReservation.adresse" />
            <x-input label="Adresse complémentaire" wire:model.defer="adresseReservation.adresse_complement" />
            <x-input label="Code postal" wire:model.defer="adresseReservation.code_postal" />
            <x-input label="Ville" wire:model.defer="adresseReservation.ville" />
            <x-select
                label="Secrétaire"
                placeholder="Sélectionner une secrétaire"
                :async-data="route('api.users')"
                option-label="full_name"
                option-value="id"
                option-description="entreprise.nom"
                wire:model.defer="adresseReservation.user_id"
            />
            <x-button info label="Enregistrer" type="submit"/>
        </form>
    </x-bloc-content>
</div>

<div>
    <x-title-section>
        <x-slot:title>
            {!! $adresseReservation->exists ? "Modification de l'adresse <span class='text-blue-500'>".$adresseReservation->adresse."</span>" : "Création d'une adresse" !!}
        </x-slot:title>
    </x-title-section>

    <x-admin.content>
        <form wire:submit.prevent="save">
            <div class="form-group">
                <x-input label="Adresse" wire:model.defer="adresseReservation.adresse" />
            </div>
            <div class="form-group">
                <x-input label="Adresse complémentaire" wire:model.defer="adresseReservation.adresse_complement" />
            </div>
            <div class="form-group">
                <x-input label="Code postal" wire:model.defer="adresseReservation.code_postal" />
            </div>
            <div class="form-group">
                <x-input label="Ville" wire:model.defer="adresseReservation.ville" />
            </div>
            <div class="form-group">
                <x-select
                    label="Secrétaire"
                    placeholder="Sélectionner une secrétaire"
                    :async-data="route('api.users')"
                    option-label="full_name"
                    option-value="id"
                    option-description="entreprise.nom"
                    wire:model.defer="adresseReservation.user_id"
                />
            </div>
            <div class="form-group">
                <x-button info label="Enregistrer" type="submit"/>
            </div>
        </form>
    </x-admin.content>
</div>

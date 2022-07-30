<div>
    <x-header>
        {!! $localisation->exists ? "Modification de <span class='text-blue-500'>".$localisation->nom."</span>" : "Création d'une localisation" !!}
    </x-header>
    <x-bloc-content>
        <form wire:submit.prevent="save" class="space-y-4">
            <x-input label="Nom" wire:model.defer="localisation.nom" />
            <x-input label="Téléphone" wire:model.defer="localisation.telephone" />
            <x-input label="Adresse" wire:model.defer="localisation.adresse" />
            <x-input label="Adresse complémentaire" wire:model.defer="localisation.adresse_complement" />
            <x-input label="Code postal" wire:model.defer="localisation.code_postal" />
            <x-input label="Ville" wire:model.defer="localisation.ville" />
            <x-toggle wire:model.defer="localisation.is_actif" label="Actif" md />
            <x-button type="submit" label="Enregistrer" primary sm />
        </form>
    </x-bloc-content>
</div>

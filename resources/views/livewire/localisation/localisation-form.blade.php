<div>
    <x-title-section>
        <x-slot:title>
            {!! $localisation->exists ? "Modification de <span class='text-blue-500'>".$localisation->nom."</span>" : "Création d'une localisation" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save">
            <div class="form-group">
                <x-input label="Nom" wire:model.defer="localisation.nom" />
            </div>
            <div class="form-group">
                <x-input label="Téléphone" wire:model.defer="localisation.telephone" />
            </div>
            <div class="form-group">
                <x-input label="Adresse" wire:model.defer="localisation.adresse" />
            </div>
            <div class="form-group">
                <x-input label="Adresse complémentaire" wire:model.defer="localisation.adresse_complement" />
            </div>
            <div class="form-group">
                <x-input label="Code postal" wire:model.defer="localisation.code_postal" />
            </div>
            <div class="form-group">
                <x-input label="Ville" wire:model.defer="localisation.ville" />
            </div>
            <div class="form-group">
                <x-toggle id="actif" lg wire:model.defer="localisation.is_actif" label="Actif"/>
            </div>
            <div class="form-group">
                <x-button info type="submit" label="Enregistrer" />
            </div>
        </form>
    </x-admin.content>
</div>

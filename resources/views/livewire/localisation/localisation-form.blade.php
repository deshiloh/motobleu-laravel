<div>
    <x-title-section>
        <x-slot:title>
            {!! $localisation->exists ? "Modification de <span class='text-blue-500'>".$localisation->nom."</span>" : "Création d'une localisation" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save" class="space-y-2">
            <x-input label="Nom" wire:model.defer="localisation.nom" />
            <x-input label="Téléphone" wire:model.defer="localisation.telephone" />
            <x-input label="Adresse" wire:model.defer="localisation.adresse" />
            <x-input label="Adresse complémentaire" wire:model.defer="localisation.adresse_complement" />
            <x-input label="Code postal" wire:model.defer="localisation.code_postal" />
            <x-input label="Ville" wire:model.defer="localisation.ville" />
            <x-form.toggle wire:model.defer="localisation.is_actif">
                Actif
            </x-form.toggle>
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary btn-sm">
                    Enregistrer
                </button>
            </div>
        </form>
    </x-admin.content>
</div>

<div>
    <x-title-section>
        <x-slot:title>
            {!! $entreprise->exists ? "Modification de l'entreprise <span class='text-blue-500'>".$entreprise->nom."</span>" : "Création de l'entreprise" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save">
            <div class="form-group">
                <x-input label="Nom" wire:model.defer="entreprise.nom"/>
            </div>
            <div class="form-group">
                <x-toggle label="Actif" wire:model.defer="entreprise.is_actif" lg />
            </div>
            <div class="mt-6">
                <x-button label="Enregistrer" type="submit" info />
            </div>
        </form>
    </x-admin.content>
</div>

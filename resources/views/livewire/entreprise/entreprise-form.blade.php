<div>
    <x-header>
        {!! $entreprise->exists ? "Modification de l'entreprise <span class='text-blue-500'>".$entreprise->nom."</span>" : "Création de l'entreprise" !!}
    </x-header>
    <form wire:submit.prevent="save" class="space-y-3">
        <div class="form-control">
            <x-input label="Nom" wire:model.defer="entreprise.nom"/>
        </div>
        <x-toggle label="Actif" wire:model.defer="entreprise.is_actif" md/>
        <x-button type="submit" label="Enregistrer" sm primary />
    </form>
</div>

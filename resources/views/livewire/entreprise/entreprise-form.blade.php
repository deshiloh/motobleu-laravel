<div>
    <x-header>
        {!! $entreprise->exists ? "Modification de l'entreprise <span class='text-blue-500'>".$entreprise->nom."</span>" : "Création de l'entreprise" !!}
    </x-header>
    <x-bloc-content>
        <form wire:submit.prevent="save" class="space-y-3">
            <x-input label="Nom" wire:model.defer="entreprise.nom"/>
            <x-input label="Nom du responsable / Directeur" wire:model.defer="entreprise.responsable_name"/>
            <x-toggle label="Actif" wire:model.defer="entreprise.is_actif" md/>
            <x-button type="submit" label="Enregistrer" sm primary />
        </form>
    </x-bloc-content>
</div>

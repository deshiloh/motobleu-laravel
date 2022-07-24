<div>
    <x-header>
        {!! $costCenter->exists ? "Modication du Cost Center <span class='text-blue-500'>".$costCenter->nom."</span>" : "Création d'un Cost Center" !!}
    </x-header>

    <x-bloc-content>
        <form wire:submit.prevent="save" class="space-y-3">
            <x-input label="Nom" wire:model.defer="costCenter.nom" />
            <x-toggle wire:model.defer="costCenter.is_actif" md label="Actif" />
            <x-button type="submit" primary sm label="Enregistrer" />
        </form>
    </x-bloc-content>
</div>

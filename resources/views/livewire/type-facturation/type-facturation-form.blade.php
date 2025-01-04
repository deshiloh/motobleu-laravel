<div>
    <x-header>
        {!! $typeFacturation->exists ? "Modification de <span class='text-blue-500'>".$typeFacturation->nom."</span>" : "Création d'un type de facturation" !!}
    </x-header>
    <x-bloc-content>
        <form wire:submit="save" class="space-y-4">
            <x-input label="Nom" wire:model.live="typeFacturation.nom" />
            <x-button type="submit" label=" Enregistrer" primary sm />
        </form>
    </x-bloc-content>
</div>

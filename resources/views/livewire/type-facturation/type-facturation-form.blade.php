<div>
    <x-title-section>
        <x-slot:title>
            {!! $typeFacturation->exists ? "Modification de <span class='text-blue-500'>".$typeFacturation->nom."</span>" : "Création d'un type de facturation" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save" class="space-y-3">
            <x-input label="Nom" wire:model.defer="typeFacturation.nom" />
            <button type="submit" class="btn btn-primary btn-sm">
                Enregistrer
            </button>
        </form>
    </x-admin.content>
</div>

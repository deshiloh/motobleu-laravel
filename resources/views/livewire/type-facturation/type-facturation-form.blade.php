<div>
    <x-title-section>
        <x-slot:title>
            {!! $typeFacturation->exists ? "Modification de <span class='text-blue-500'>".$typeFacturation->nom."</span>" : "Création d'un type de facturation" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save">
            <div class="form-group">
                <x-input label="Nom" wire:model.defer="typeFacturation.nom" />
            </div>
            <div class="form-group">
                <x-button label="Enregistrer" type="submit" info spinner="save"/>
            </div>
        </form>
    </x-admin.content>
</div>

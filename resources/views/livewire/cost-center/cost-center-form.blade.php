<div>
    <x-title-section>
        <x-slot:title>
            {!! $costCenter->exists ? "Modication du Cost Center <span class='text-blue-500'>".$costCenter->nom."</span>" : "Création d'un Cost Center" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save">
            <div class="form-group">
                <x-input label="Nom" wire:model.defer="costCenter.nom" />
            </div>
            <div class="form-group">
                <x-toggle lg label="Actif" wire:model.defer="costCenter.is_actif"/>
            </div>
            <div class="form-group">
                <x-button label="Enregistrer" info type="submit" />
            </div>
        </form>
    </x-admin.content>
</div>

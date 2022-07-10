<div>
    <x-title-section>
        <x-slot:title>
            {!! $entreprise->exists ? "Modification de l'entreprise <span class='text-blue-500'>".$entreprise->nom."</span>" : "Création de l'entreprise" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save" class="space-y-2">
            <div class="form-control">
                <x-input label="Nom" wire:model.defer="entreprise.nom"/>
            </div>
            <x-form.toggle wire:model.defer="entreprise.is_actif">
                Actif
            </x-form.toggle>
            <button type="submit" class="btn btn-primary btn-sm">
                Enregistrer
            </button>
        </form>
    </x-admin.content>
</div>

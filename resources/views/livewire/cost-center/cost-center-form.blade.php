<div>
    <x-title-section>
        <x-slot:title>
            {!! $costCenter->exists ? "Modication du Cost Center <span class='text-blue-500'>".$costCenter->nom."</span>" : "Création d'un Cost Center" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save" class="space-y-3">
            <x-input label="Nom" wire:model.defer="costCenter.nom" />
            <x-form.toggle wire:model.defer="costCenter.is_actif">
                Actif
            </x-form.toggle>
            <button type="submit" class="btn btn-primary btn-sm">
                Enregistrer
            </button>
        </form>
    </x-admin.content>
</div>

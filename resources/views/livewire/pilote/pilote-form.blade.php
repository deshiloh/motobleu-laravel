<div>
    <x-title-section>
        <x-slot:title>
            {!! $pilote->exists ? "Modification du pilote <span class='text-blue-500'>".$pilote->full_name."</span>" : "Création d'un piloe" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save">
            <div class="form-group">
                <x-input label="Nom" wire:model.defer="pilote.nom" />
            </div>
            <div class="form-group">
                <x-input label="Prénom" wire:model.defer="pilote.prenom" />
            </div>
            <div class="form-group">
                <x-input label="Téléphone" wire:model.defer="pilote.telephone" />
            </div>
            <div class="form-group">
                <x-input label="Adresse email" wire:model.defer="pilote.email" type="email"/>
            </div>
            <div class="form-group">
                <x-input label="Nom de l'entreprise" wire:model.defer="pilote.entreprise" />
            </div>
            <div class="form-group">
                <x-input label="adresse" wire:model.defer="pilote.adresse" />
            </div>
            <div class="form-group">
                <x-input label="Adresse complément" wire:model.defer="pilote.adresse_complement" />
            </div>
            <div class="form-group">
                <x-input label="Code postal" wire:model.defer="pilote.code_postal" />
            </div>
            <div class="form-group">
                <x-input label="Ville" wire:model.defer="pilote.ville" />
            </div>
            <div class="mt-5">
                <x-button type="submit" label="Enregistrer" info />
            </div>
        </form>
    </x-admin.content>
</div>

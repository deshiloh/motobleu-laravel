<div>
    <x-header>
        {!! $pilote->exists ? "Modification du pilote <span class='text-blue-500'>".$pilote->full_name."</span>" : "Création d'un pilote" !!}
    </x-header>
    <x-bloc-content>
        <form wire:submit.prevent="save" class="space-y-4">
            <x-input label="Nom *" wire:model.defer="pilote.nom" />
            <x-input label="Prénom *" wire:model.defer="pilote.prenom" />
            <x-input label="Téléphone *" wire:model.defer="pilote.telephone" />
            <x-input label="Adresse email *" wire:model.defer="pilote.email" type="email"/>
            <x-input label="Nom de l'entreprise" wire:model.defer="pilote.entreprise" />
            <x-input label="adresse" wire:model.defer="pilote.adresse" />
            <x-input label="Adresse complément" wire:model.defer="pilote.adresse_complement" />
            <x-input label="Code postal" wire:model.defer="pilote.code_postal" />
            <x-input label="Ville" wire:model.defer="pilote.ville" />
            <x-input label="Commission" type="number" step="0.01" wire:model="commission"/>
            <x-button type="submit" primary sm >Enregistrer</x-button>
        </form>
    </x-bloc-content>
</div>

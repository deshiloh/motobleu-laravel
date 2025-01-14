<div>
    <x-header>
        {!! $user->exists ? "Modification du compte <span class='text-blue-500'>".$user->full_name."</span>" : "Création d'un compte" !!}
    </x-header>
    <x-bloc-content>
        <form wire:submit="save" wire:loading.class="opacity-25" class="space-y-3">
            <x-input label="Nom" wire:model.live="user.nom" id="nom"/>
            <x-input label="Prénom" wire:model.live="user.prenom" id="prenom" />
            <x-input type="email" label="Adresse email" wire:model.live="user.email" id="email" />
            <x-input type="tel" label="Téléphone" wire:model.live="user.telephone" id="telephone"/>
            <x-input label="Adresse" wire:model.live="user.adresse" id="adresse" />
            <x-input label="Adresse Bis" wire:model.live="user.adresse_bis" id="adresse_bis" />
            <x-input label="Code postal" wire:model.live="user.code_postal" id="code_postal"/>
            <x-input label="Ville" wire:model.live="user.ville" id="ville"/>
            <x-toggle md label="Compte admin (Un compte admin peut consulter ses factures)" wire:model="isAdmin" id="is_admin"/>
            <x-toggle md label="Compte actif" wire:model="isActif" id="is_actif" />
            <x-button type="submit" label="Enregistrer" wire:loading.class="disabled" primary sm id="submit_button" />
        </form>
    </x-bloc-content>
</div>

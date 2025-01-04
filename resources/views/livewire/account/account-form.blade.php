<div>
    <x-header>
        {!! $user->exists ? "Modification du compte <span class='text-blue-500'>".$user->full_name."</span>" : "Création d'un compte" !!}
    </x-header>
    <x-bloc-content>
        <form wire:submit="save" wire:loading.class="opacity-25" class="space-y-3">
            <x-input label="Nom" wire:model.live="user.nom"/>
            <x-input label="Prénom" wire:model.live="user.prenom" />
            <x-input type="email" label="Adresse email" wire:model.live="user.email" />
            <x-input type="tel" label="Téléphone" wire:model.live="user.telephone"/>
            <x-input label="Adresse" wire:model.live="user.adresse" />
            <x-input label="Adresse Bis" wire:model.live="user.adresse_bis" />
            <x-input label="Code postal" wire:model.live="user.code_postal"/>
            <x-input label="Ville" wire:model.live="user.ville"/>
            <x-toggle md label="Compte admin (Un compte admin peut consulter ses factures)" wire:model="isAdmin"/>
            <x-toggle md label="Compte actif" wire:model="isActif" />
            <x-button type="submit" label="Enregistrer" wire:loading.class="disabled" primary sm />
        </form>
    </x-bloc-content>
</div>

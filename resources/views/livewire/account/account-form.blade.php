<div>
    <x-header>
        {!! $form->user && $form->user->exists ? "Modification du compte <span class='text-blue-500'>".$form->user->full_name."</span>" : "Création d'un compte" !!}
    </x-header>
    <x-bloc-content>
        <form wire:submit.prevent="save" wire:loading.class="opacity-25" class="space-y-3">
            <x-input label="Nom" wire:model.defer="form.nom"/>
            <x-input label="Prénom" wire:model.defer="form.prenom" />
            <x-input type="email" label="Adresse email" wire:model.defer="form.email" />
            <x-input type="tel" label="Téléphone" wire:model.defer="form.telephone"/>
            <x-input label="Adresse" wire:model.defer="form.adresse" />
            <x-input label="Adresse Bis" wire:model.defer="form.adresse_bis" />
            <x-input label="Code postal" wire:model.defer="form.code_postal"/>
            <x-input label="Ville" wire:model.defer="form.ville"/>
            <x-toggle md label="Compte admin (Un compte admin peut consulter ses factures)" wire:model.defer="isAdmin"/>
            <x-toggle md label="Compte actif" wire:model.defer="form.is_actif" />
            <x-button type="submit" label="Enregistrer" wire:loading.class="disabled" primary sm />
        </form>
    </x-bloc-content>
</div>

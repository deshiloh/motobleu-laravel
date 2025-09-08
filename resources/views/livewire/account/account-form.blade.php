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
            <div>
                @if($form->user && $form->user->hasRole('super admin'))
                    <x-toggle md label="Compte admin additionnel (en plus du rôle Super Admin)" wire:model.defer="isAdmin"/>
                    <div class="mt-1 text-sm text-blue-600 bg-blue-50 px-3 py-2 rounded-md border border-blue-200">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <strong>Super Administrateur</strong> - Cet utilisateur dispose de privilèges étendus permanents
                        </div>
                    </div>
                @else
                    <x-toggle md label="Compte admin (Un compte admin peut consulter ses factures)" wire:model.defer="isAdmin"/>
                @endif
            </div>
            <x-toggle md label="Compte actif" wire:model.defer="form.is_actif" />
            <x-button type="submit" label="Enregistrer" wire:loading.class="disabled" primary sm />
        </form>
    </x-bloc-content>
</div>

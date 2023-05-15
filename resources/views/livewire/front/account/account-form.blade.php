<div>
    <x-front.card>
        <x-front.title>
            {{ $contextNewUser ? "Création d'une secrétaire" : "Modification de la secrétaire {$user->full_name}" }}
            <x-slot:button>
                <x-button flat label="{{ __('Retour à la liste') }}" href="{{ route('front.user.list') }}" />
            </x-slot:button>
        </x-front.title>
        <x-errors class="mb-3"/>
        <form wire:submit.prevent="save" class="space-y-3">
            <x-input label="{{ __('Nom') }}" wire:model.defer="user.nom"/>
            <x-input label="{{ __('Prénom') }}" wire:model.defer="user.prenom" />
            <x-input type="email" label="{{ __('Adresse email') }}" wire:model.defer="user.email" />
            <x-input type="tel" label="{{ __('Téléphone') }}" wire:model.defer="user.telephone"/>
            <x-input label="{{ __('Adresse') }}" wire:model.defer="user.adresse" />
            <x-input label="{{ __('Adresse Bis') }}" wire:model.defer="user.adresse_bis" />
            <x-input label="{{ __('Code postal') }}" wire:model.defer="user.code_postal"/>
            <x-input label="{{ __('Ville') }}" wire:model.defer="user.ville"/>
            <x-toggle md label="{{ __('Compte actif') }}" wire:model.defer="user.is_actif" />
            <x-toggle md label="{{ __('Compte admin Ardian') }}" wire:model.defer="user.is_admin" />
            <x-button type="submit" label="{{ __('Enregistrer') }}" wire:loading.class="disabled" primary sm spinner="save"/>
        </form>
    </x-front.card>
</div>

<div>
    <x-front.card>
        <x-front.title>
            {{ $contextNewUser ? "Création d'une secrétaire" : "Modification de la secrétaire {$user->full_name}" }}
            <x-slot:button>
                <x-button flat label="{{ __('Retour à la liste') }}" href="{{ route('front.user.list') }}" />
            </x-slot:button>
        </x-front.title>
        <x-errors class="mb-3"/>
        <form wire:submit="save" class="space-y-3">
            <x-input label="{{ __('Nom') }}" wire:model="user.nom"/>
            <x-input label="{{ __('Prénom') }}" wire:model="user.prenom" />
            <x-input type="email" label="{{ __('Adresse email') }}" wire:model="user.email" />
            <x-input type="tel" label="{{ __('Téléphone') }}" wire:model="user.telephone"/>
            <x-input label="{{ __('Adresse') }}" wire:model="user.adresse" />
            <x-input label="{{ __('Adresse Bis') }}" wire:model="user.adresse_bis" />
            <x-input label="{{ __('Code postal') }}" wire:model="user.code_postal"/>
            <x-input label="{{ __('Ville') }}" wire:model="user.ville"/>
            <x-toggle md label="{{ __('Compte actif') }}" wire:model="user.is_actif" />
            <x-toggle md label="{{ __('Compte admin Ardian') }}" wire:model="user.is_admin" />
            <x-button type="submit" label="{{ __('Enregistrer') }}" wire:loading.class="disabled" primary sm spinner="save"/>
        </form>
    </x-front.card>
</div>

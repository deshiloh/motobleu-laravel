<div>
    <x-title-section>
        <x-slot:title>
            {!! $user->exists ? "Modification du compte <span class='text-blue-500'>".$user->full_name."</span>" : "Création d'un compte" !!}
        </x-slot:title>
    </x-title-section>

    <x-admin.content>
        <form wire:submit.prevent="save" wire:loading.class="opacity-25" class="space-y-2">
            <x-input label="Nom" wire:model.defer="user.nom"/>
            <x-input label="Prénom" wire:model.defer="user.prenom" />
            <x-input type="email" label="Adresse email" wire:model.defer="user.email" />
            <x-input type="tel" label="Téléphone" wire:model.defer="user.telephone"/>
            <x-input label="Adresse" wire:model.defer="user.adresse" />
            <x-input label="Adresse Bis" wire:model.defer="user.adresse_bis" />
            <x-input label="Code postal" wire:model.defer="user.code_postal"/>
            <x-input label="Ville" wire:model.defer="user.ville"/>
            <x-select
                label="Entreprise"
                placeholder="Sélectionner une entreprise"
                :async-data="route('api.entreprises')"
                option-label="nom"
                option-value="id"
                wire:model.defer="user.entreprise_id"
            />
            <x-form.toggle wire:model.defer="user.is_admin_ardian" >
                Compte admin Ardian
            </x-form.toggle>
            <x-form.toggle wire:model.defer="user.is_actif" >
                Compte actif
            </x-form.toggle>
            <button type="submit" class="btn btn-sm btn-primary" wire:loading.class="disabled">Enregistrer</button>
        </form>
    </x-admin.content>
</div>

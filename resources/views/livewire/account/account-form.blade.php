<div>
    <x-title-section>
        <x-slot:title>
            {!! $user->exists ? "Modification du compte <span class='text-blue-500'>".$user->full_name."</span>" : "Création d'un compte" !!}
        </x-slot:title>
    </x-title-section>

    <x-admin.content>
        <form wire:submit.prevent="save" wire:loading.class="opacity-25">
            <div class="form-group">
                <x-input label="Nom" wire:model.defer="user.nom"/>
            </div>

            <div class="form-group">
                <x-input label="Prénom" wire:model.defer="user.prenom" />
            </div>

            <div class="form-group">
                <x-input type="email" label="Adresse email" wire:model.defer="user.email" />
            </div>

            <div class="form-group">
                <x-input type="tel" label="Téléphone" wire:model.defer="user.telephone"/>
            </div>

            <div class="form-group">
                <x-input label="Adresse" wire:model.defer="user.adresse" />
            </div>

            <div class="form-group">
                <x-input label="Adresse Bis" wire:model.defer="user.adresse_bis" />
            </div>

            <div class="form-group">
                <x-input label="Code postal" wire:model.defer="user.code_postal"/>
            </div>

            <div class="form-group">
                <x-input label="Ville" wire:model.defer="user.ville"/>
            </div>

            <div class="form-group">
                <x-select
                    label="Entreprise"
                    placeholder="Sélectionner une entreprise"
                    :async-data="route('api.entreprises')"
                    option-label="nom"
                    option-value="id"
                    wire:model.defer="user.entreprise_id"
                />
            </div>

            <div class="form-group">
                <x-toggle lg wire:model.defer="user.is_admin_ardian" label="Compte admin Ardian" />
            </div>

            <div class="from-group">
                <x-toggle lg wire:model.defer="user.is_actif" label="Compte actif" />
            </div>

            <div class="form-group">
                <x-button type="submit" info label="Enregister" />
            </div>
        </form>
    </x-admin.content>
</div>

<div>
    <x-title-section>
        <x-slot:title>
            {!! $passager->exists ? "Modification du passager <span class='text-blue-500'>". $passager->nom ."</span>" : "Création d'un passager" !!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save" wire:loading.class="opacity-25">
            <div class="form-group">
                <x-input label="Nom & prénom" wire:model.defer="passager.nom" />
            </div>
            <div class="form-group">
                <x-input type="email" label="Adresse email" wire:model.defer="passager.email" />
            </div>
            <div class="form-group">
                <x-input type="tel" label="Téléphone bureau" wire:model.defer="passager.telephone"/>
            </div>
            <div class="form-group">
                <x-input label="Téléphone portable" wire:model.defer="passager.portable" />
            </div>
            <div class="form-group">
                <x-select
                    label="Secrétaire"
                    placeholder="Sélectionner une secrétaire"
                    :async-data="route('api.users')"
                    option-label="full_name"
                    option-value="id"
                    option-description="entreprise.nom"
                    wire:model.defer="passager.user_id"
                />
            </div>
            <div class="form-group">
                <x-select
                    label="Cost Center"
                    placeholder="Sélectionner une secrétaire"
                    :async-data="route('api.cost_center')"
                    option-label="nom"
                    option-value="id"
                    option-description="entreprise.nom"
                    wire:model.defer="passager.cost_center_id"
                />
            </div>
            <div class="form-group">
                <x-select
                    label="Type Facturation"
                    placeholder="Sélectionner un type de facturation"
                    :async-data="route('api.type_facturation')"
                    option-label="nom"
                    option-value="id"
                    option-description="entreprise.nom"
                    wire:model.defer="passager.type_facturation_id"
                />
            </div>
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary btn-sm">
                    Enregistrer
                </button>
            </div>
        </form>
    </x-admin.content>
</div>

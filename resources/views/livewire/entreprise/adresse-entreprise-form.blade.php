<div>
    <x-title-section>
        <x-slot:title>
            {!! $adresseEntreprise->exists ? "Modification de l'adresse de <span class='text-blue-500'>". $adresseEntreprise->type->name."</span> de <span class='text-blue-500'>". $entreprise->nom."</span>" : "Création d'une adresse pour <span class='text-blue-500'>".$entreprise->nom."</span>"!!}
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="save">
            <div class="form-group">
                <x-input label="Nom de l'adresse" wire:model.defer="adresseEntreprise.nom" />
            </div>
            <div class="form-group">
                <x-input label="Adresse email de contact" wire:model.defer="adresseEntreprise.email" type="email" hint="En cas de type facturation, elle sera utilisée pour l'envoi de la facture"/>
            </div>
            <div class="form-group">
                <x-input label="Adresse" wire:model.defer="adresseEntreprise.adresse" />
            </div>
            <div class="form-group">
                <x-input label="Adresse complémentaire" wire:model.defer="adresseEntreprise.adresse_complement" />
            </div>
            <div class="form-group">
                <x-input label="Code postal" wire:model.defer="adresseEntreprise.code_postal" />
            </div>
            <div class="form-group">
                <x-input label="Ville" wire:model.defer="adresseEntreprise.ville" />
            </div>
            <div class="form-group">
                <x-input label="TVA" wire:model.defer="adresseEntreprise.tva" />
            </div>
            <div class="form-group">
                <x-native-select label="Type de l'adresse" wire:model="adresseEntreprise.type">
                    @foreach(\App\Enum\AdresseEntrepriseTypeEnum::cases() as $type)
                        <option value="{{ $type->value }}">{{ $type->name }}</option>
                    @endforeach
                </x-native-select>
            </div>
            <div class="form-group">
                <x-button label="Enregistrer" info type="submit" />
            </div>
        </form>
    </x-admin.content>
</div>

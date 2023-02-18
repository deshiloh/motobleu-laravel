<div>
    <x-header>
        {!! $adresseEntreprise->exists ? "Modification de l'adresse de <span class='text-blue-500'>". $adresseEntreprise->type->name."</span> de <span class='text-blue-500'>". $entreprise->nom."</span>" : "Création d'une adresse pour <span class='text-blue-500'>".$entreprise->nom."</span>"!!}
    </x-header>
    <x-bloc-content>
        <form wire:submit.prevent="save" class="space-y-3">
            <x-input label="Nom de l'adresse" wire:model.defer="adresseEntreprise.nom" />
            <x-input label="Adresse email de contact" wire:model.defer="adresseEntreprise.email" type="email" hint="En cas de type facturation, elle sera utilisée pour l'envoi de la facture"/>
            <x-input label="Adresse" wire:model.defer="adresseEntreprise.adresse" />
            <x-input label="Adresse complémentaire" wire:model.defer="adresseEntreprise.adresse_complement" />
            <x-input label="Code postal" wire:model.defer="adresseEntreprise.code_postal" />
            <x-input label="Ville" wire:model.defer="adresseEntreprise.ville" />
            <x-input label="TVA" wire:model.defer="adresseEntreprise.tva" />
            <x-native-select label="Type de l'adresse" wire:model="adresseEntreprise.type" placeholder="Sélectionnez un type d'adresse">
                @foreach(\App\Enum\AdresseEntrepriseTypeEnum::cases() as $type)
                    <option value="{{ $type->value }}">{{ $type->name }}</option>
                @endforeach
            </x-native-select>
            <x-button type="submit" sm primary label="Enregistrer" />
        </form>
    </x-bloc-content>
</div>

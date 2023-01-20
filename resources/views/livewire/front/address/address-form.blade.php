<div>
    <x-front.card>
        <x-front.title>
            {{ $adresseReservation->exists ? "Formulaire d'édition d'adresse" : "Formulaire de création d'adresse" }}
            <x-slot:button>
                <x-button flat label="{{ __('Retour à la liste') }}" href="{{ route('front.address.list') }}"/>
            </x-slot:button>
        </x-front.title>
        <form wire:submit.prevent="save" class="space-y-4">
            <x-input label="Adresse" wire:model.defer="adresseReservation.adresse" />
            <x-input label="Adresse complémentaire" wire:model.defer="adresseReservation.adresse_complement" />
            <x-input label="Code postal" wire:model.defer="adresseReservation.code_postal" />
            <x-input label="Ville" wire:model.defer="adresseReservation.ville" />
            <x-button primary label="Enregistrer" type="submit" wire:loading.attr="disabled"/>
        </form>
    </x-front.card>
</div>

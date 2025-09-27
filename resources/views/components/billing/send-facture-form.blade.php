@props(['facture', 'email'])

<form wire:submit.prevent="sendFactureAction" id="factureForm" class="space-y-4">
    <x-input label="Email" wire:model.defer="email.address"/>
    <x-textarea wire:model="email.message" label="Message" rows="4"/>
    <x-toggle wire:model.defer="facture.is_acquitte" label="Facture acquittée"/>
    <x-textarea wire:model.defer="facture.information" label="Informations" rows="3"/>
    <x-textarea wire:model.defer="facture.adresse_client" label="Adresse client" rows="3"/>
    <x-textarea wire:model.defer="facture.adresse_facturation" label="Adresse Facturation" rows="3"/>

    <div class="flex gap-2">
        <x-button wire:click="sendEmailTestAction" primary sm type="button" icon="envelope">
            Envoi d'un email de test
        </x-button>
        <x-button wire:click="exportAction" info sm type="button" icon="arrow-down-tray">
            Récap. des courses
        </x-button>
    </div>
</form>
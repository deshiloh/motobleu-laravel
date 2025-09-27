@props(['facture', 'uniqID', 'email'])

<x-modal wire:model.defer="isSendFactureModalOpened" width="full">
    @if($facture)
    <x-card title="Envoi de la facture" wire:key="facture">
        <x-errors class="mb-4"/>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <iframe src="/admin/facturations/{{ $facture->id }}/show?uniq={{ $uniqID }}#view=FitH&toolbar=1" class="w-full h-full"></iframe>
            </div>
            <div>
                <form wire:submit.prevent="sendFactureAction" id="factureForm" class="space-y-4">
                    <x-input label="Email" wire:model.defer="email.address"/>
                    <x-tinymce wire:model="email.message"/>
                    <x-toggle wire:model.defer="facture.is_acquitte" label="Facture acquittée"/>
                    <x-tinymce wire:model.defer="facture.information" label="Informations"/>
                    <x-tinymce wire:model.defer="facture.adresse_client" label="Adresse client"/>
                    <x-tinymce wire:model.defer="facture.adresse_facturation" label="Adresse Facturation"/>
                    <x-button wire:click="sendEmailTestAction" primary sm type="button" icon="envelope">Envoi d'un email de test</x-button>
                    <x-button wire:click="exportAction" info sm type="button" icon="arrow-down-tray">Récap. des courses</x-button>
                </form>
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button x-on:click="close" sm >
                    Annuler
                </x-button>
                <x-button type="submit" form="factureForm" primary sm >
                    @if($facture->statut == \App\Enum\BillStatut::COMPLETED)
                        Envoyer
                    @else
                        Finaliser et envoyer
                    @endif
                </x-button>
            </div>
        </x-slot>
    </x-card>
    @endif
</x-modal>
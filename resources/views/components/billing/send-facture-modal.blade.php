@props(['facture', 'uniqID', 'email'])

<x-modal wire:model.defer="isSendFactureModalOpened" width="full">
    @if($facture)
    <x-card title="Envoi de la facture" wire:key="facture">
        <x-errors class="mb-4"/>

        <div class="grid md:grid-cols-2 gap-6">
            {{-- Prévisualisation PDF --}}
            <div>
                <iframe
                    src="/admin/facturations/{{ $facture->id }}/show?uniq={{ $uniqID }}#view=FitH&toolbar=1"
                    class="w-full h-full">
                </iframe>
            </div>

            {{-- Formulaire d'envoi --}}
            <div>
                <x-billing.send-facture-form :facture="$facture" :email="$email" />
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button x-on:click="close" sm>Annuler</x-button>
                <x-button type="submit" form="factureForm" primary sm>
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

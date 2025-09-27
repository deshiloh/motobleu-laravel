@props(['facture', 'entreprise', 'selectedMonth', 'selectedYear'])

<div class="flex gap-4">
    {{-- Bouton retour --}}
    @if($facture->statut == \App\Enum\BillStatut::COMPLETED)
        <x-button href="{!! route('admin.facturations.index') !!}" label="Retourner à la liste" sm />
    @else
        <x-button href="{!! route('admin.facturations.edition', [
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
        ]) !!}" label="Retourner à la liste" sm />
    @endif

    {{-- Bouton annulation si montant à 0 --}}
    @if($facture->montant_ttc == 0)
        <x-button red wire:click="cancelBill">
            Finaliser, annuler et ne pas envoyer
        </x-button>
    @endif

    {{-- Bouton d'envoi ou alerte adresse manquante --}}
    @if($entreprise?->hasBilledAddress())
        @if($facture->statut == \App\Enum\BillStatut::COMPLETED)
            <x-button wire:click="openSendFactureModal" label="Envoyer la facturation" positive sm />
        @else
            <x-button wire:click="openSendFactureModal" label="Finaliser et envoyer la facturation" positive sm />
        @endif
    @else
        <div class="p-2 text-sm text-yellow-700 bg-yellow-100 rounded-lg dark:bg-yellow-200 dark:text-yellow-800" role="alert">
            <span class="font-medium">Attention</span> L'entreprise n'as pas d'adresse de facturation
        </div>
    @endif
</div>
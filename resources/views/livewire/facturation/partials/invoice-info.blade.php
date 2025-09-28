@props(['facture', 'isAcquitte'])

<x-bloc-content>
    <h3 class="text-xl font-semibold">Informations de la facture</h3>

    <div class="mb-3 flex flex-col">
        <span>Date de création : {{ $facture->created_at->format('d/m/Y H:i') }}</span>
        @if($facture->billed_at)
            <span>Date de finalisation : {{ $facture->billed_at->format('d/m/Y H:i') }}</span>
        @endif
    </div>

    <div>Référence : <span class="text-motobleu font-semibold">{{ $facture->reference }}</span></div>
    <div>Période : {{ sprintf("%02d", $facture->month) }} / {{ $facture->year }}</div>
    <div>Adresse de facturation : {!! $facture->address_bill_inline !!}</div>
    <div>Adresse de client : {!! $facture->address_client_inline !!}</div>

    <div class="mt-3">
        @if($facture->statut === \App\Enum\BillStatut::COMPLETED)
            <x-toggle left-label="Facture acquittée" wire:model.live="isAcquitte"/>
        @else
            La facture pourra être acquittée qu'une fois finalisée
        @endif
    </div>
</x-bloc-content>

@props(['montant_ttc'])

@php
    $montant_ttc = round($montant_ttc, 2);
    $prixHT = $montant_ttc / 1.10;
    $prixTVA = $montant_ttc - $prixHT;
@endphp

<div class="flex justify-end py-4">
    <div class="flex flex-col space-y-3 text-right dark:text-white">
        <div>
            <strong>Montant H.T :</strong> {{ number_format($prixHT, 2, ',', ' ') }} €
        </div>
        <div>
            <strong>TVA 10% :</strong> {{ number_format($prixTVA, 2, ',', ' ') }} €
        </div>
        <div>
            <strong>Montant TTC :</strong> {{ number_format($montant_ttc, 2, ',', ' ') }} €
        </div>
    </div>
</div>
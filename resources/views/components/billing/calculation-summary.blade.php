@props(['calculation'])

@php
    use App\ValueObjects\FactureCalculation;

    if (is_array($calculation)) {
        $calc = FactureCalculation::fromTTC($calculation['montant_ttc'] ?? 0);
    } elseif ($calculation instanceof FactureCalculation) {
        $calc = $calculation;
    } else {
        $calc = FactureCalculation::fromTTC(floatval($calculation ?? 0));
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col space-y-3 text-right dark:text-white']) }}>
    <div>
        <strong>Montant H.T :</strong> {{ $calc->formatMontantHT() }}
    </div>
    <div>
        <strong>TVA {{ $calc->formatTauxTVA() }} :</strong> {{ $calc->formatMontantTVA() }}
    </div>
    <div>
        <strong>Montant TTC :</strong> {{ $calc->formatMontantTTC() }}
    </div>
</div>
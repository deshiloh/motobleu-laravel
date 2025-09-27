@props(['amount', 'currency' => 'EUR', 'locale' => 'fr_FR'])

@php
    if (is_numeric($amount)) {
        if ($locale === 'fr_FR' && $currency === 'EUR') {
            $formatted = number_format($amount, 2, ',', ' ') . ' €';
        } else {
            $fmt = new NumberFormatter($locale, NumberFormatter::CURRENCY);
            $formatted = $fmt->formatCurrency($amount, $currency);
        }
    } else {
        $formatted = '0,00 €';
    }
@endphp

<span {{ $attributes->merge(['class' => 'text-gray-900 dark:text-gray-100']) }}>
    {{ $formatted }}
</span>
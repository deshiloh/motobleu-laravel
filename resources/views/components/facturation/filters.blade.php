@props(['months', 'selectedMonth', 'selectedYear', 'entrepriseSearch'])

@php
    $startedYear = \Carbon\Carbon::now()->subYears(4)->year;
    $endYear = \Carbon\Carbon::now()->addYears(4)->year;
@endphp

<div class="border-b border-gray-200 pb-3 mb-4">
    <div class="grid grid-cols-4 gap-6">
        <x-select
            :searchable="false"
            :clearable="false"
            label="Mois"
            placeholder="Sélectionner un mois"
            wire:model.live="selectedMonth"
        >
            @foreach($months as $numMonth => $labelMonth)
                <x-select.option :label="$labelMonth" :value="$numMonth" />
            @endforeach
        </x-select>

        <x-select
            label="Année"
            placeholder="Sélectionner une année"
            wire:model.live="selectedYear"
            :searchable="false"
            :clearable="false"
        >
            @for($startedYear; $startedYear <= $endYear; $startedYear ++)
                <x-select.option :label="$startedYear" :value="$startedYear" />
            @endfor
        </x-select>

        <x-select
            label="Entreprise"
            placeholder="Sélectionner une entreprise"
            :async-data="route('api.entreprises')"
            option-label="nom"
            option-value="id"
            wire:model.live="entrepriseSearch"
        />
    </div>
</div>

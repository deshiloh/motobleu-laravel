@props(['months', 'selectedMonth', 'selectedYear', 'entrepriseSearch'])

@php
    $startedYear = \Carbon\Carbon::now()->subYears(4)->year;
    $endYear = \Carbon\Carbon::now()->addYears(4)->year;
@endphp

<div class="border-b border-gray-200 pb-3 mb-4">
    <div class="grid grid-cols-4 gap-6">
        <x-native-select
            label="Mois"
            placeholder="Sélectionner un mois"
            wire:model="selectedMonth"
        >
            @foreach($months as $numMonth => $labelMonth)
                <option value="{{ $numMonth }}">{{ $labelMonth }}</option>
            @endforeach
        </x-native-select>

        <x-native-select
            label="Année"
            placeholder="Sélectionner une année"
            wire:model="selectedYear"
        >
            @for($startedYear; $startedYear <= $endYear; $startedYear ++)
                <option value="{{ $startedYear }}">{{ $startedYear }}</option>
            @endfor
        </x-native-select>

        <x-select
            label="Entreprise"
            placeholder="Sélectionner une entreprise"
            :async-data="route('api.entreprises')"
            option-label="nom"
            option-value="id"
            wire:model="entrepriseSearch"
        />
    </div>
</div>
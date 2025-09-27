@props(['monthModel' => 'selectedMonth', 'yearModel' => 'selectedYear', 'months' => [], 'years' => []])

<div class="grid grid-cols-2 gap-4">
    <x-native-select
        label="Mois"
        placeholder="Sélectionner un mois"
        wire:model.live="{{ $monthModel }}"
    >
        @foreach($months as $numMonth => $labelMonth)
            <option value="{{ $numMonth }}">{{ $labelMonth }}</option>
        @endforeach
    </x-native-select>

    <x-native-select
        label="Année"
        placeholder="Sélectionner une année"
        wire:model.live="{{ $yearModel }}"
    >
        @foreach($years as $year => $yearLabel)
            <option value="{{ $year }}">{{ $yearLabel }}</option>
        @endforeach
    </x-native-select>
</div>
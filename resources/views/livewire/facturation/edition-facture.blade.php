@php
    $months = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre',
 ]
@endphp
<div>
    <x-title-section>
        <x-slot:title>
            @if($this->entreprise && $month && $year)
                Édition de facturation pour l'entrerise <span class="text-blue-500">{{ $this->entreprise->nom }}</span>
                @else
                Édition de facturation
            @endif
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <x-select
                    label="Entreprise"
                    wire:model="entrepriseId"
                    placeholder="Sélectionner une entreprise"
                    :async-data="route('api.entreprises')"
                    option-label="nom"
                    option-value="id"
                />
            </div>
            <div>
                <x-native-select label="Mois" wire:model="month">
                    @foreach($months as $monthNumber => $monthLabel)
                        <option value="{{ $monthNumber }}">{{ $monthLabel }}</option>
                    @endforeach
                </x-native-select>
            </div>
            <div>
                <x-native-select label="Année" wire:model="year">
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                </x-native-select>
            </div>
        </div>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Passager</x-datatable.th>
                    <x-datatable.th>Départ</x-datatable.th>
                    <x-datatable.th>Arrivée</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @foreach($this->reservations as $reservation)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                    </x-datatable.tr>
                @endforeach
            </x-slot:body>
        </x-datatable>
    </x-admin.content>
</div>

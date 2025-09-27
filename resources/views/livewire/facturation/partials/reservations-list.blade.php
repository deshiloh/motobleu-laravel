@props(['facture'])

<x-bloc-content>
    <div class="text-2xl mb-3 dark:text-white">Liste des réservations</div>

    <x-datatable>
        <x-slot:headers>
            <tr>
                <x-datatable.th>Référence</x-datatable.th>
                <x-datatable.th>Date</x-datatable.th>
                <x-datatable.th>Passager</x-datatable.th>
                <x-datatable.th>Départ</x-datatable.th>
                <x-datatable.th>Arrivée</x-datatable.th>
                <x-datatable.th>Validation (€)</x-datatable.th>
                <x-datatable.th>Tarif (€)</x-datatable.th>
                <x-datatable.th>Majoration (%)</x-datatable.th>
                <x-datatable.th>Compléments (€)</x-datatable.th>
                <x-datatable.th>Commentaire</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot:headers>
        <x-slot:body>
            @php
                $validation = 0;
                $fmt = new NumberFormatter('fr_FR', NumberFormatter::CURRENCY);
            @endphp
            @forelse($facture->reservations()->orderBy('pickup_date')->get() as $reservation)
                @php
                    $currentAmount = $this->calculTotal($reservation);
                    $montant_ttc += $currentAmount;
                    $validation += $currentAmount;
                @endphp
                <x-datatable.tr :success="$reservation->tarif !== null" x-data="billDatas({{ json_encode($reservation) }})">
                    <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                    <x-datatable.td>
                        @if($reservation->tarif !== null)
                            {{ $fmt->formatCurrency($validation, 'EUR') }}
                        @endif
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="w-24 xl:w-full">
                            <x-input x-model="formData.tarif" type="number" step="0.01" placeholder="Tarif de la course" />
                        </div>
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="w-24 xl:w-full">
                            <x-input type="number" step="0.01" x-model="formData.majoration" placeholder="Majoration de la course"/>
                        </div>
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="w-24 xl:w-full">
                            <x-input x-model="formData.complement" type="number" step="0.01" placeholder="Complément de la course"/>
                        </div>
                    </x-datatable.td>
                    <x-datatable.td>
                        <x-textarea x-model="formData.comment_facture" placeholder="Votre commentaire" />
                    </x-datatable.td>
                    <x-datatable.td>
                        <x-button primary sm label="Valider" @click="submission"/>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td colspan="11">
                        <div class="text-center">
                             <div class="block text-xl">Aucune réservation trouvée</div>
                            Seule les réservations qui sont facturées apparaissent ici
                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot:body>
    </x-datatable>

    @include('livewire.facturation.partials.pricing-summary', ['montant_ttc' => $montant_ttc])
</x-bloc-content>
<div>
    <div class="text-xl">
        Récapitulatif des courses
    </div>
    <div class="border-b border-gray-500 py-3 flex items-center justify-between">
        <div class="flex space-x-4">
            <x-datetime-picker
                id="from_date"
                without-timezone
                label="Date de début"
                placeholder="Date de début"
                wire:model="dateDebut"
                :without-time="true"
            />
            <x-datetime-picker
                id="to_date"
                without-timezone
                label="Date de fin"
                placeholder="Date de fin"
                wire:model="dateFin"
                :without-time="true"
            />
        </div>
        <div>
            <x-button icon="document-download" label="Exporter" xs />
        </div>
    </div>
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th>Référence</x-datatable.th>
                <x-datatable.th>Secrétaire</x-datatable.th>
                <x-datatable.th>Date</x-datatable.th>
                <x-datatable.th>Départ</x-datatable.th>
                <x-datatable.th>Arrivée</x-datatable.th>
                <x-datatable.th>Prix</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($reservations as $reservation)
                <x-datatable.tr>
                    <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->passager->user->full_name }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                    <x-datatable.td>
                        @php
                            // TODO à faire quand les prix seront mis en place.
                        @endphp
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <tr>
                    <x-datatable.td class="text-center bg-gray-700" colspan="6">
                        Aucune réservations
                    </x-datatable.td>
                </tr>
            @endforelse
        </x-slot>
        <x-slot name="tfoot">
        </x-slot>
    </x-datatable>
    {{ $reservations->links() }}
</div>

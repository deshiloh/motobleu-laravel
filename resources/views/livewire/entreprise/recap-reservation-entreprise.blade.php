<div>
    <div class="flex items-center space-x-4">
        <span class="text-xl">Récapitulatif des courses</span>
        <button class="btn btn-primary gap-2 btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Exporter
        </button>
    </div>
    <div class="py-3 flex items-center justify-between">
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

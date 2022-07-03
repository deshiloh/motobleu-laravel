<div>
    <div class="flex justify-between">
        <x-datatable.search wire:model="search" />
        @if($countReservationToConfirmed > 0)
            <a href="{{ route('admin.reservations.index', ['querySort' => 'not_confirmed']) }}" class="bg-yellow-400 inline-block p-4 mb-5 rounded-lg hover:bg-yellow-500 transition-all duration-200">
                {{ $countReservationToConfirmed }} réservation(s) à confirmer
            </a>
        @endif
    </div>
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th sortable wire:click="sortBy('id')" :direction="$sortDirection">Référence</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('pickup_date')" :direction="$sortDirection">Date</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('entreprise')" :direction="$sortDirection">Entreprise</x-datatable.th>
                <x-datatable.th>Passager</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('localisation_from')" :direction="$sortDirection">Départ</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('localisation_to')" :direction="$sortDirection">Arrivée</x-datatable.th>
                <x-datatable.th>Etat</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($reservations as $reservation)
                <x-datatable.tr>
                    <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->passager->user->entreprise->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                    <x-datatable.td>
                        @if($reservation->is_cancel && !$reservation->is_confirmed)
                            <span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900">Annulée</span>
                        @endif

                        @if($reservation->is_confirmed && !$reservation->is_cancel)
                                <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">Confirmée</span>
                        @endif

                        @if(!$reservation->is_confirmed && !$reservation->is_cancel)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900 whitespace-nowrap">à confirmer</span>
                        @endif
                    </x-datatable.td>
                    <x-datatable.td>
                        <x-dropdown>
                            <x-slot name="trigger">
                                <x-button label="Actions" info sm />
                            </x-slot>

                            <x-dropdown.item label="Détails" wire:click="goTo('{{ route('admin.reservations.show', ['reservation' => $reservation->id]) }}')" />
                            <x-dropdown.item label="Éditer" wire:click="goTo('{{ route('admin.reservations.edit', ['reservation' => $reservation->id]) }}')" />
                        </x-dropdown>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="8">Aucune réservation</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
        <x-slot name="tfoot">
        </x-slot>
    </x-datatable>
    <div class="mt-4 px-1">
        {{ $reservations->links('components.datatable.pagination') }}
    </div>
</div>

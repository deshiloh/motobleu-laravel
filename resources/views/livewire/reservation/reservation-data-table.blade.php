<div>
    @if($countReservationToConfirmed > 0)
        <a href="{{ route('admin.reservations.index', ['querySort' => 'not_confirmed']) }}" class="alert alert-warning shadow-lg mb-4">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <div>
                    <span class="font-bold">{{ $countReservationToConfirmed }}</span> réservation(s) à confirmer
                </div>
            </div>
        </a>
    @endif

    <x-datatable.search wire:model="search" />
    <div class="flex justify-between">


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
                                <x-button label="Actions" primary sm />
                            </x-slot>

                            <x-dropdown.item label="Détails" href="{{ route('admin.reservations.show', ['reservation' => $reservation->id]) }}" />
                            <x-dropdown.item separator label="Éditer" href="{{ route('admin.reservations.edit', ['reservation' => $reservation->id]) }}" />
                        </x-dropdown>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="8">Aucune réservation</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <div class="px-3 py-4">
        {{ $reservations->links('components.datatable.pagination') }}
    </div>
</div>

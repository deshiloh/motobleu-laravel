<div>
    @if($countReservationToConfirmed > 0)
        <!-- This example requires Tailwind CSS v2.0+ -->
        <a href="{{ route('admin.reservations.index', ['querySort' => 'not_confirmed']) }}">
            <div class="rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: solid/exclamation -->
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Réservations à confirmer</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Vous avez <span class="font-bold">{{ $countReservationToConfirmed }}</span> réservation(s) à confirmer </p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    @endif

    <x-datatable.search wire:model="search" />
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
                    <x-datatable.td>{{ $reservation->entreprise?->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $reservation->localdisplay_from }}</x-datatable.td>
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
                        <div class="flex space-x-2">
                            <x-button.circle icon="eye" primary sm href="{{ route('admin.reservations.show', ['reservation' => $reservation->id]) }}" />
                            <x-button.circle icon="pencil" info sm href="{{ route('admin.reservations.edit', ['reservation' => $reservation->id]) }}" />
                        </div>
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

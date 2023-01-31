<div>
    <div class="pb-3 border-b border-gray-200 dark:border-gray-600 sm:flex sm:items-center sm:justify-between">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Récapitulatif des courses</h3>
    </div>
    <div class="py-3 grid grid-cols-3 gap-5">
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
                        {{ $reservation->tarif  ?? "NC" }} €
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <tr>
                    <x-datatable.td class="text-center dark:bg-gray-700" colspan="6">
                        Aucune réservations
                    </x-datatable.td>
                </tr>
            @endforelse
        </x-slot>
        <x-slot name="tfoot">
        </x-slot>
    </x-datatable>
    <x-front.pagination :pagination="$reservations" :per-page="$perPage"/>
</div>

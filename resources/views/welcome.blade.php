<x-layout>
    <x-center-bloc>
        <!-- This example requires Tailwind CSS v2.0+ -->
        <div class="my-4">
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2">

                <livewire:home.reservations-chart />

                <div class="relative bg-white dark:bg-slate-800 pt-5 px-4 pb-12 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
                    <dt>
                        <div class="absolute bg-indigo-500 rounded-md p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="ml-16 text-sm font-medium text-gray-500 truncate">Entreprises</p>
                    </dt>
                    <dd class="pb-6 flex items-baseline sm:pb-7">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-200">
                            {{ \App\Models\Entreprise::count() }}
                        </p>
                        <div class="absolute bottom-0 inset-x-0 bg-gray-50 dark:bg-slate-700 px-4 py-4 sm:px-6">
                            <div class="text-sm">
                                <a href="{{ route('admin.entreprises.index') }}" class="font-medium text-indigo-600 dark:text-indigo-500 hover:text-indigo-500"> Voir <span class="sr-only"> Avg. Click Rate stats</span></a>
                            </div>
                        </div>
                    </dd>

                </div>
            </dl>
        </div>
    </x-center-bloc>

    <x-bloc-content>
        <h3 class="text-xl mb-4 dark:text-gray-100">Réservation à confirmer</h3>
        <x-datatable>
            <x-slot:headers>
                <tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Arrivée</x-datatable.th>
                    <x-datatable.th>Départ</x-datatable.th>
                    <x-datatable.th>Entreprise</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse(\App\Models\Reservation::where('is_confirmed', false)->where('is_cancel', false)->get() as $reservation)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->entreprise->nom }}</x-datatable.td>
                        <x-datatable.td>
                            <x-button.circle href="{{ route('admin.reservations.show', ['reservation' => $reservation->id]) }}" icon="eye" primary />
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="6">Aucune réservation à confirmer</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </x-bloc-content>
</x-layout>

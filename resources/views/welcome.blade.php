<x-layout>
    <div class="my-4 px-4">
        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-2">

            <div
                class="relative bg-white dark:bg-slate-800 pt-5 px-4 pb-12 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
                <div class="absolute pointer-events-none">
                    <div class="flex">
                        <div class="flex flex-col ml-4">
                            <p class="text-xl font-medium text-gray-500 truncate">Total réservations</p>
                            <div class="text-5xl font-semibold text-gray-900 dark:text-gray-200">
                                <livewire:components.reservations-count/>
                            </div>
                        </div>
                    </div>
                </div>
                <dd class="pb-6 flex items-baseline sm:pb-7">
                    <div class="absolute bottom-0 inset-x-0 bg-gray-50 dark:bg-slate-700 px-4 py-4 sm:px-6">
                        <div class="grid grid-cols-2">
                            <div>
                                <livewire:home.company-reservation-stats/>
                            </div>
                            <div class="flex justify-end">
                                <livewire:home.company-reservation-stats :is-last="true"/>
                            </div>
                        </div>
                    </div>
                </dd>
                <div class="mb-6">
                    <livewire:home.reservations-chart/>
                </div>
            </div>

            <div
                class="relative bg-white dark:bg-slate-800 pt-5 px-4 pb-12 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
                <div class="absolute">
                    <div class="flex">
                        <div class="flex flex-col ml-4">
                            <p class="text-xl font-medium text-gray-500 truncate">Total facturé</p>
                            <div class="text-5xl font-semibold text-gray-900 dark:text-gray-200">
                                @php
                                    $period = \Carbon\CarbonPeriod::create(\Carbon\Carbon::now()->startOfYear(), '1 month', \Carbon\Carbon::now()->endOfMonth());
                                    $months = array_map(fn($item) => $item->month, $period->toArray());

                                    $ttc = \App\Models\Facture::whereIn('month', $months)
                                    ->where('year', \Carbon\Carbon::now()->year)
                                    ->where('statut', \App\Enum\BillStatut::COMPLETED)
                                    ->sum('montant_ttc');
                                @endphp
                                {{ number_format($ttc, 2, '.', ' ') }} €
                            </div>
                        </div>
                    </div>
                </div>
                <dd class="pb-6 flex items-baseline sm:pb-7">
                    <div class="absolute bottom-0 inset-x-0 bg-gray-50 dark:bg-slate-700 px-4 py-4 sm:px-6">
                        <div class="grid grid-cols-2">
                            <div>
                                Informations
                            </div>
                            <div class="flex justify-end">
                                Informations
                            </div>
                        </div>
                    </div>
                </dd>
                <div class="mb-6">
                    <livewire:home.home-facturation-chart/>
                </div>
            </div>
        </dl>
    </div>

    <x-bloc-content>
        <h3 class="text-xl mb-4 dark:text-gray-100">Réservation à confirmer</h3>
        <x-datatable>
            <x-slot:headers>
                <tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Départ</x-datatable.th>
                    <x-datatable.th>Arrivée</x-datatable.th>
                    <x-datatable.th>Entreprise</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse(\App\Models\Reservation::where('statut', \App\Enum\ReservationStatus::Created)->get() as $reservation)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->entreprise->nom }}</x-datatable.td>
                        <x-datatable.td>
                            <x-button.circle
                                href="{{ route('admin.reservations.show', ['reservation' => $reservation->id]) }}"
                                icon="eye" primary/>
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

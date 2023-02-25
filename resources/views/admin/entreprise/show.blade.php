<x-layout>
    <x-header>
        Fiche de l'entreprise <span class="text-blue-700">{{ $entreprise->nom }}</span>
        <x-slot:right>
            <div class="flex space-x-2">
                <x-button
                    href="{{ route('admin.entreprises.edit', ['entreprise' => $entreprise]) }}"
                    label="Modifier l'entreprise"
                    sm
                    primary
                />
            </div>
        </x-slot:right>
    </x-header>
    <div class="space-y-5">
        <x-bloc-content>
            <div class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Nom/prénom du responsable : {{ $entreprise->responsable_name }}
            </div>
        </x-bloc-content>
        <div class="px-4">
            <div class="grid max-lg:grid-cols-1 grid-cols-2 gap-5 dark:bg-slate-800 bg-white rounded-lg border border-gray-200 dark:border-black">
                <div class="dark:bg-slate-800 rounded-lg p-3 relative">
                    <div class="absolute flex flex-col pointer-events-none">
                        <div class="text-3xl text-gray-500">
                            Réservations
                        </div>
                        <div class="text-4xl dark:text-white text-gray-900 dark:text-gray-200">
                            @php
                                $nbReservationForCompany = \App\Models\Reservation::where(
                                    'entreprise_id',
                                    $entreprise->id
                                    )
                                    ->whereBetween('pickup_date', [
                                        \Carbon\Carbon::now()->startOfYear(),
                                        \Carbon\Carbon::now()->endOfMonth()
                                    ])
                                    ->count();
                            @endphp
                            {{ $nbReservationForCompany }}
                        </div>
                    </div>
                    <livewire:entreprise.reservation-entreprise-chart :entreprise="$entreprise"/>
                </div>
                <div class="dark:bg-slate-800 rounded-lg p-3 relative">
                    <div class="absolute flex flex-col pointer-events-none">
                        <div class="text-3xl text-gray-500">
                            Facturation
                        </div>
                        <div class="text-4xl dark:text-white text-gray-900 dark:text-gray-200">
                            @php
                                $period = \Carbon\CarbonPeriod::create(\Carbon\Carbon::now()->startOfYear(), '1 month', \Carbon\Carbon::now()->endOfMonth());
                                $months = array_map(fn($item) => $item->month, $period->toArray());
                                    $ht = \App\Models\Facture::whereHas(
                                        'reservations',
                                        function (\Illuminate\Database\Eloquent\Builder $builder) use ($entreprise) {
                                            $builder->where('entreprise_id', $entreprise->id);
                                        })
                                        ->whereIn('month', $months)
                                        ->where('year', \Carbon\Carbon::now()->year)
                                        ->sum('montant_ht');
                                    $ttc = $ht + ($ht * 0.1);
                            @endphp
                            {{ number_format($ttc, 2, '.', ' ') }} €
                        </div>
                    </div>
                    <livewire:entreprise.facturation-chart :entreprise="$entreprise"/>
                </div>
            </div>
        </div>
        <x-bloc-content>
            <livewire:entreprise.users-entreprise-data-table :entreprise="$entreprise" />
        </x-bloc-content>
        <x-bloc-content>
            <livewire:entreprise.adresses-entreprise-data-table :entreprise="$entreprise"/>
        </x-bloc-content>
        <x-bloc-content>
            <livewire:entreprise.recap-reservation-entreprise :entreprise="$entreprise" />
        </x-bloc-content>
    </div>
</x-layout>

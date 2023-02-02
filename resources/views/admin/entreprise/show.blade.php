<x-layout>
    <x-header>
        Fiche de l'entreprise <span class="text-blue-700">{{ $entreprise->nom }}</span>
        <x-slot:right>
            <div class="flex space-x-2">
                @if($entreprise->adresseEntreprises()->count() < 2)
                    <x-button
                        href="{{ route('admin.entreprises.adresses.create', ['entreprise' => $entreprise]) }}"
                        sm
                        primary
                        label="Ajouter une adresse entreprise"
                    />
                @endif
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
                Responsable / Directeur : {{ $entreprise->responsable_name }}
            </div>
        </x-bloc-content>
        <div class="px-4">
            <div class="grid grid-cols-3 gap-5 bg-white rounded-lg border border-gray-200">
                <div class="dark:bg-slate-800 rounded-lg p-3 relative">
                    <div class="absolute flex flex-col">
                        <div class="text-3xl text-gray-500">
                            Réservations
                        </div>
                        <div class="text-4xl dark:text-white text-gray-900 dark:text-gray-200">
                            @php
                                $nbReservationForCompany = \App\Models\Reservation::where(
                                    'entreprise_id',
                                    $entreprise->id
                                    )->count();
                            @endphp
                            {{ $nbReservationForCompany }}
                        </div>
                    </div>
                    <livewire:entreprise.reservation-entreprise-chart :entreprise="$entreprise"/>
                </div>
                <div class="dark:bg-slate-800 rounded-lg p-3 relative">
                    <div class="absolute flex flex-col">
                        <div class="text-3xl text-gray-500">
                            Facturation
                        </div>
                        <div class="text-4xl dark:text-white text-gray-900 dark:text-gray-200">
                            @php
                                $ht = \App\Models\Facture::whereHas(
                                    'reservations',
                                    function (\Illuminate\Database\Eloquent\Builder $builder) use ($entreprise) {
                                        $builder->where('entreprise_id', $entreprise->id);
                                    })->sum('montant_ht');
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

<div>
    <x-header>
        Statistique annuelle
    </x-header>

    <x-bloc-content>
        <div class="grid grid-cols-4 md:grid-cols-1 gap-4 mb-4">
            <x-native-select
                label="Année"
                :options="$years"
                wire:model="selectedYear"
            />
        </div>

        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>Nom</x-datatable.th>
                    <x-datatable.th>Chiffre d'affaire</x-datatable.th>
                    <x-datatable.th>Commissions</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @php
                    $totalCa = 0;
                    $totalCom = 0;
                @endphp
                @forelse($pilotes as $pilote)
                    @php
                        $revenu = ($pilote->chiffre_affaire * 100) / 100;
                        $totalCa += $revenu;

                        $commission = ($pilote->total_commission * 100) / 100;
                        $totalCom += $commission;
                    @endphp
                    <x-datatable.tr>
                        <x-datatable.td>
                            {{ $pilote->nom }} {{ $pilote->prenom }}
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ number_format($revenu, 2, '.', '') }} €
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ number_format($commission, 2, '.', '') }} €
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="3" class="text-center">Rien à afficher</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
                <x-datatable.tr>
                    <x-datatable.td>
                        Total
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="font-bold">{{ number_format($totalCa, 2, '.', '') }} €</div>
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="font-bold">{{ number_format($totalCom, 2, '.', '') }} €</div>
                    </x-datatable.td>
                </x-datatable.tr>
            </x-slot:body>
        </x-datatable>
        <x-front.pagination :pagination="$pilotes" :per-page="$perPage" />
    </x-bloc-content>
</div>
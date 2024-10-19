<div>
    <x-header>
        Statistiques pilotes mensuelle
    </x-header>
    <x-bloc-content>
        <div class="grid md:grid-cols-4 grid-cols-1 gap-4 mb-4">
            <x-native-select
                label="Mois"
                :options="$months"
                option-label="valeur"
                option-value="id"
                wire:model="selectedMonth"
            />
            <x-native-select
                label="Année"
                :options="$years"
                wire:model="selectedYear"
            />
        </div>

        <x-datatable>
            <x-slot:headers>
                <tr>
                    <x-datatable.th>Pilote</x-datatable.th>
                    <x-datatable.th>Chiffre d'affaire</x-datatable.th>
                    <x-datatable.th>Commissions</x-datatable.th>
                </tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($pilotes as $pilote)
                    @php
                        $revenu = ($pilote->chiffre_affaire * 100) / 100;

                        $totalCom = ($pilote->total_commission * 100) / 100;
                    @endphp
                    <x-datatable.tr>
                        <x-datatable.td>
                            {{ $pilote->nom }} {{ $pilote->prenom }}
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ number_format($revenu, 2, '.', '') }} €
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ number_format($totalCom, 2, '.', '') }} €
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <tr>
                        <x-datatable.td colspan="3" class="text-center">Rien à afficher</x-datatable.td>
                    </tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
        <x-front.pagination :pagination="$pilotes" :per-page="$perPage" />
    </x-bloc-content>

</div>

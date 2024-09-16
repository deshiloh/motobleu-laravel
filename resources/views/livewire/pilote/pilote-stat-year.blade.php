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
                    <x-datatable.th>Total</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($pilotes as $pilote)
                    @php
                        $com = $pilote->total_encompte * 0.15;
                        $totalAmount = $pilote->total_encompte - $com;
                        $totalAmount = ($totalAmount * 100) / 100;

                        $revenu = $pilote->total_encompte + $pilote->total_encaisse;
                        $revenu = ($revenu * 100) / 100;
                    @endphp
                    <x-datatable.tr>
                        <x-datatable.td>
                            {{ $pilote->nom }} {{ $pilote->prenom }}
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ number_format($revenu, 2, '.', '') }} €
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ number_format($totalAmount, 2, '.', '') }} €
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="3" class="text-center">Rien à afficher</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
        <x-front.pagination :pagination="$pilotes" :per-page="$perPage" />
    </x-bloc-content>
</div>

<div>
    <x-header>
        Liste des factures
    </x-header>
    <x-bloc-content>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <x-input label="Rechercher" icon="search" wire:model="search" />
            </div>
            <div>
                <x-native-select
                    label="Acquittée"
                    wire:model="isAcquitte"
                >
                    <option value="0">Tout</option>
                    <option value="1">Non acquittée</option>
                    <option value="2">Acquittée</option>
                </x-native-select>
            </div>
            <div>
                <x-select
                    label="Entreprise"
                    wire:model="entreprise"
                    placeholder="Rechercher une entreprise"
                    :async-data="route('api.entreprises')"
                    option-label="nom"
                    option-value="id"
                />
            </div>
        </div>
        <x-datatable>
            <x-slot:headers>
                <tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Acquittée</x-datatable.th>
                    <x-datatable.th>Entreprise</x-datatable.th>
                    <x-datatable.th>Montant</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($facturations as $facture)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $facture->reference }}</x-datatable.td>
                        <x-datatable.td>{{ $facture->created_at->format('d/m/Y') }}</x-datatable.td>
                        <x-datatable.td>
                            @if($facture->is_acquitte)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"> Oui </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"> Non </span>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ $facture->reservations->first()->entreprise->nom }}
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ number_format($facture->montant_ttc, 2, ',', ' ') }} €
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-button label="Voir" href="{{ route('admin.facturations.show', ['facture' => $facture->id]) }}" target="_blank" icon="eye" info sm />

                            <x-button label="Liste des courses" icon="view-list" primary sm href="{!! route('admin.facturations.edition', [
                                    'selectedMonth' => $facture->month,
                                    'selectedYear' => $facture->year,
                                    'factureSelected' => $facture->id
                                ]
                            ) !!}" />
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="5" class="text-center">Aucune facture</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
        <x-front.pagination :pagination="$facturations" :per-page="$perPage"/>
    </x-bloc-content>
</div>

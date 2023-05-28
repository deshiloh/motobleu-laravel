<div>
    <x-header>
        Export de factures
    </x-header>
    <x-bloc-content>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-4">
            <div>
                <x-datetime-picker
                    label="Date de début"
                    placeholder="Sélectionnez une date de début"
                    :without-time="true"
                    display-format="DD/MM/YYYY"
                    wire:model="dateDebut"
                    :clearable="false"
                />
            </div>
            <div>
                <x-datetime-picker
                    label="Date de fin"
                    placeholder="Sélectionnez une date de fin"
                    :without-time="true"
                    display-format="DD/MM/YYYY"
                    wire:model="dateFin"
                    :clearable="false"
                />
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
            <div>
                <x-native-select
                    label="Factures par page"
                    :options="[10, 20, 30, 100, 200]"
                    wire:model="perPage"
                />
            </div>
            <div class="flex items-end">
                <x-button label="Exporter" primary wire:click="exportAction"/>
            </div>
        </div>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Date création</x-datatable.th>
                    <x-datatable.th>Acquittée</x-datatable.th>
                    <x-datatable.th>Entreprise</x-datatable.th>
                    <x-datatable.th>Montant</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @php
                    $fmt = new NumberFormatter('fr_FR', NumberFormatter::CURRENCY);
                @endphp
                @forelse($factures as $facture)
                    <x-datatable.tr>
                        <x-datatable.td>
                            <a href="{!! route('admin.facturations.edition', [
                                    'selectedMonth' => $facture->month,
                                    'selectedYear' => $facture->year,
                                    'factureSelected' => $facture->id
                                ]
                            ) !!}" class="text-motobleu hover:underline">
                                {{ $facture->reference }}
                            </a>
                        </x-datatable.td>
                        <x-datatable.td>{{ $facture->created_at->format('d/m/Y') }}</x-datatable.td>
                        <x-datatable.td>
                            @if($facture->is_acquitte)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"> Oui </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"> Non </span>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ $facture->reservations->first()->entreprise->nom  ?? "Non disponible"}}
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ $fmt->formatCurrency($facture->montant_ttc, 'EURO') }}
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="5">
                            <div class="text-center">Aucune factures trouvés</div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
        <x-front.pagination :pagination="$factures" :per-page="$perPage"/>
    </x-bloc-content>
</div>

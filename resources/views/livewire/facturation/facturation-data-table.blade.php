<div>
    <x-header>
        Liste des factures
    </x-header>
    <x-bloc-content>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div class="relative">
                <x-input label="Rechercher" icon="magnifying-glass" wire:model.live.debounce.300ms="search" />
                <div wire:loading wire:target="search" class="absolute right-2 top-8">
                    <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <x-select
                    :clearable="false"
                    :searchable="false"
                    label="Acquittée"
                    wire:model.live="isAcquitte"
                >
                    <x-select.option label="Tout" :value="0" />
                    <x-select.option label="Non acquittée" :value="1" />
                    <x-select.option label="Acquittée" :value="2" />
                </x-select>
            </div>
            <div>
                <x-select
                    label="Entreprise"
                    wire:model.live="entreprise"
                    placeholder="Rechercher une entreprise"
                    :async-data="route('api.entreprises')"
                    option-label="nom"
                    option-value="id"
                />
            </div>
        </div>
        <div wire:loading.class="opacity-50">
            <x-datatable>
            <x-slot:headers>
                <tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Statut</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Acquittée</x-datatable.th>
                    <x-datatable.th>Entreprise</x-datatable.th>
                    <x-datatable.th>Montant</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </tr>
            </x-slot:headers>
            <x-slot:body>
                @php
                    $fmt = new NumberFormatter('fr_FR', NumberFormatter::CURRENCY);
                @endphp
                @forelse($facturations as $facture)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $facture->reference }}</x-datatable.td>
                        <x-datatable.td>
                            @if($facture->statut === \App\Enum\BillStatut::COMPLETED)
                                <x-front.badge success>
                                    Finalisée
                                </x-front.badge>
                            @endif
                            @if($facture->statut === \App\Enum\BillStatut::CANCEL)
                                <x-front.badge danger>
                                    Annulée
                                </x-front.badge>
                            @endif
                            @if($facture->statut === \App\Enum\BillStatut::CREATED)
                                <x-front.badge warning>
                                    En cours
                                </x-front.badge>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>{{ $facture->created_at->format('d/m/Y') }}</x-datatable.td>
                        <x-datatable.td>
                            @if($facture->statut !== \App\Enum\BillStatut::CREATED)
                                @if($facture->is_acquitte)
                                    <x-button wire:click="toggleAcquitte({{ $facture }})" positive sm>Oui</x-button>
                                @else
                                    <x-button wire:click="toggleAcquitte({{ $facture }})" negative sm>Non</x-button>
                                @endif
                            @else
                                @if($facture->is_acquitte)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"> Oui </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"> Non </span>
                                @endif
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ $facture->reservations->first()->entreprise->nom }}
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ $fmt->formatCurrency($facture->montant_ttc, 'EUR') }}
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-button label="Voir" href="{{ route('admin.facturations.show', ['facture' => $facture->id]) }}" target="_blank" icon="eye" info sm />

                            <x-button label="Liste des courses" icon="view-columns" primary sm href="{!! route('admin.facturations.edition', [
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
        </div>
        <x-front.pagination :pagination="$facturations" :perPage="$perPage" />
    </x-bloc-content>
</div>

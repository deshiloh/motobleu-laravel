<div>
    <x-front.card>
        <x-front.title>
            {{ __('Liste des factures') }}
        </x-front.title>
        <x-datatable.search wire:model="search"/>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>{{ __('Référence') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Date') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Montant') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Actions') }}</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @php /** @var $facture \App\Models\Facture */ @endphp
                @forelse($factures as $facture)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $facture->reference }}</x-datatable.td>
                        <x-datatable.td>{{ $facture->created_at->format('d/m/Y') }}</x-datatable.td>
                        <x-datatable.td>{{ number_format($facture->montant_ttc, '2', ',', ' ') }} €</x-datatable.td>
                        <x-datatable.td>
                            <div class="space-x-2">
                                @can('see facture')
                                    <x-button.circle icon="eye" primary target="_blank" href="{{ route('front.invoice.show', ['facture' => $facture]) }}"/>
                                    <x-button.circle icon="view-list" positive href="{{ route('front.invoice.reservations', ['invoice' => $facture]) }}"/>
                                @endcan
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                    @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="4">
                            <div class="text-center">
                                {{ __('Aucun Facture') }}
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
        <x-front.pagination :pagination="$factures" :per-page="$perPage"/>
    </x-front.card>
</div>

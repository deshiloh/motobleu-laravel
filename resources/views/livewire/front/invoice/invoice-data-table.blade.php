<div>
    <x-front.card>
        <x-front.title>
            {{ __('Liste des factures') }}
        </x-front.title>
        <div class="relative">
            <x-datatable.search wire:model.live.debounce.300ms="search"/>
            <div wire:loading wire:target="search" class="absolute right-2 top-8">
                <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <div wire:loading.class="opacity-50">
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
                                    <x-mini-button rounded icon="eye" primary target="_blank" href="{{ route('front.invoice.show', ['facture' => $facture]) }}"/>
                                    <x-mini-button rounded icon="view-columns" positive href="{{ route('front.invoice.reservations', ['invoice' => $facture]) }}"/>
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
        </div>
        <x-front.pagination :pagination="$factures" :per-page="$perPage"/>
    </x-front.card>
</div>

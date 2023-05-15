<div>
    <x-front.card>
        <x-front.title>
            {{ __('Liste des réservations pour la facture') }} : {{ $facture->reference }}
            <x-slot:button>
                <x-button flat label="{{ __('Retour à la liste') }}" href="{{ route('front.invoice.list') }}"/>
            </x-slot:button>
        </x-front.title>
        <x-datatable.search wire:model="search"/>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>{{ __('Référence') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Assistante') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Date') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Départ') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Arrivée') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Passager') }}</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($reservations as $reservation)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->passager->user->full_name }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                    </x-datatable.tr>
                    @empty
                    <x-datatable.tr>
                        <x-datatable.td>
                            <div class="text-center">
                                {{ __('Aucune réservation') }}
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
        <x-front.pagination :pagination="$reservations" :per-page="$perPage"/>
    </x-front.card>
</div>

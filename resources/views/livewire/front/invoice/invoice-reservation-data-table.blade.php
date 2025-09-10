<div>
    <x-front.card>
        <x-front.title>
            {{ __('Liste des réservations pour la facture') }} : {{ $facture->reference }}
            <x-slot:button>
                <x-button flat label="{{ __('Retour à la liste') }}" href="{{ route('front.invoice.list') }}"/>
            </x-slot:button>
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
                        <x-datatable.td colspan="6">
                            <div class="text-center">
                                {{ __('Aucune réservation') }}
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
            </x-datatable>
        </div>
        <x-front.pagination :pagination="$reservations" :perPage="$perPage" />
    </x-front.card>
</div>

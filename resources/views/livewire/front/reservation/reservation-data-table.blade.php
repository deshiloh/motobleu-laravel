<div>
    <x-front.card>
        <div class="flex items-center justify-between">
            <x-front.title>
                {{ __('Historique des réservations') }}
            </x-front.title>
            <x-button primary label="{{ __('Nouvelle réservation') }}" icon="plus"/>
        </div>
        <hr class="my-3">
        <x-datatable>
            <x-slot name="headers">
                <tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Passager</x-datatable.th>
                    <x-datatable.th>Départ</x-datatable.th>
                    <x-datatable.th>Arrivée</x-datatable.th>
                    <x-datatable.th>Etat</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse($reservations as $reservation)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                        <x-datatable.td>
                            @if($reservation->is_cancel && !$reservation->is_confirmed)
                                <span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900">Annulée</span>
                            @endif

                            @if($reservation->is_confirmed && !$reservation->is_cancel)
                                <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">Confirmée</span>
                            @endif

                            @if(!$reservation->is_confirmed && !$reservation->is_cancel)
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900 whitespace-nowrap">à confirmer</span>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="flex space-x-2">
                                <x-button.circle icon="pencil" info sm wire:click="openModel" />
                                <x-button.circle icon="x" red sm />

                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="8">Aucune réservation</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
        </x-datatable>
        <div class="px-3 py-4">
            {{ $reservations->links('components.datatable.pagination') }}
        </div>
    </x-front.card>
    <x-modal.card title="{{ __('Demande de modification') }}" blur wire:model.defer="editAskCard">
        <div class="">
            <x-textarea label="{{ __('Message') }}" placeholder="{{ __('Votre message') }}..." />
        </div>

        <x-slot name="footer">
            <div class="flex justify-end">
                <x-button flat label="{{ __('Annuler') }}" x-on:click="close" />
                <x-button primary label="{{ __('Envoyer') }}" />
            </div>
        </x-slot>
    </x-modal.card>
</div>

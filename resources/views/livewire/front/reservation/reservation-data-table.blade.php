<div>
    <x-front.card>
        <div class="flex items-center justify-between">
            <x-front.title>
                {{ __('Historique des réservations') }}
            </x-front.title>
            <x-button primary label="{{ __('Nouvelle réservation') }}" icon="plus" href="{{ route('front.reservation.create') }}"/>
        </div>
        <hr class="my-3">
        <x-datatable.search wire:model="search" />
        <x-datatable>
            <x-slot name="headers">
                <tr>
                    <x-datatable.th>{{ __('Référence') }}</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>{{ __('Passager') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Départ') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Arrivée') }}</x-datatable.th>
                    <x-datatable.th>{{ __('État') }}</x-datatable.th>
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
                                <span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900">{{ __('Annulée') }}</span>
                            @endif

                            @if($reservation->is_confirmed && !$reservation->is_cancel)
                                <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">{{ __('Confirmée') }}</span>
                            @endif

                            @if(!$reservation->is_confirmed && !$reservation->is_cancel)
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900 whitespace-nowrap">{{ __('à confirmer') }}</span>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="flex space-x-2">
                                @can('edit reservation')
                                    <x-button.circle icon="pencil" info sm wire:click="openAskEditModal({{ $reservation }})" />
                                @endcan
                                @can('delete reservation')
                                    <x-button.circle icon="x" red sm wire:click="openAskCancelModal({{ $reservation }})"/>
                                @endcan
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="8">{{ __('Aucune réservation') }}</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
        </x-datatable>
        @if($reservations->total() > $perPage)
            <div class="pt-4">
                {{ $reservations->links('components.datatable.pagination') }}
            </div>
        @endif
    </x-front.card>
    <x-modal.card title="{{ __('Demande de modification') }}" blur wire:model.defer="editAskCard">
        <form class="w-full" id="udpapteDemandeForm" wire:submit.prevent="sendUpdateReservationEmail">
            @if($selectedReservation)
                <p class="mb-4">{{ __('Votre demande concerne la réservation') }} <span class="font-bold">{{ $selectedReservation->reference }}</span></p>
            @endif
             <div class="mb-3">
                 <x-errors />
             </div>
            <x-textarea label="{{ __('Message') }}" placeholder="{{ __('Votre message') }}..." wire:model.defer="message"/>
        </form>

        <x-slot name="footer">
            <div class="flex justify-end">
                <x-button flat label="{{ __('Annuler') }}" wire:click="closeModal" />
                <x-button primary label="{{ __('Envoyer') }}" form="udpapteDemandeForm" type="submit"/>
            </div>
        </x-slot>
    </x-modal.card>


    <x-modal.card title="{{ __('Demande annulation') }}" blur wire:model.defer="askCancelCard">
        @if($selectedReservation != null)
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: mini/x-circle -->
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Attention !</h3>
                        <div class="mt-2 text-sm text-red-700">
                            {{ __('Vous êtes sur le point de demander une annulation de la réservation') }} : <span class="font-bold">{{ $selectedReservation->reference }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <x-slot name="footer">
            <div class="flex justify-end">
                <x-button flat label="{{ __('Annuler') }}" wire:click="closeModal" />
                <x-button primary label="{{ __('Confirmer') }}" wire:click="sendCancelReservationEmail"/>
            </div>
        </x-slot>
    </x-modal.card>
</div>

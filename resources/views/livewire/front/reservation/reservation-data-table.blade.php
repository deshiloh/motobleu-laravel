<div>
    <x-front.card>
        <div class="flex justify-between">
            <div class="text-2xl">Bonjour <span class="font-bold">{{ ucfirst(Auth::user()->prenom) }}</span></div>
            @can('create reservation')
                <x-button primary label="{{ __('Nouvelle réservation') }}" icon="plus" href="{{ route('front.reservation.create') }}" />
            @endcan
        </div>

    </x-front.card>

    <x-front.card>

        <x-front.title>
            {{ __('Historique des réservations') }}
        </x-front.title>

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
                            @switch($reservation->statut)
                                @case(\App\Enum\ReservationStatus::Created)
                                    <x-front.badge warning>
                                        à confirmer
                                    </x-front.badge>
                                    @break
                                @case(\App\Enum\ReservationStatus::Canceled)
                                    <x-front.badge danger>
                                        Annulée
                                    </x-front.badge>
                                    @break
                                @case(\App\Enum\ReservationStatus::CanceledToPay)
                                    <x-front.badge warning-secondary>
                                        Annulée facturée
                                    </x-front.badge>
                                    @break
                                @case(\App\Enum\ReservationStatus::Confirmed)
                                    <x-front.badge success>
                                        Confirmée
                                    </x-front.badge>
                                    @break
                            @endswitch
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="flex space-x-2">
                                @if(
                                    $reservation->statut == \App\Enum\ReservationStatus::Created ||
                                    $reservation->statut == \App\Enum\ReservationStatus::Confirmed
                                )
                                    @can('edit reservation')
                                        <x-button.circle icon="pencil" info sm wire:click="openAskEditModal({{ $reservation }})" />
                                    @endcan
                                    @can('delete reservation')
                                        <x-button.circle icon="x" red sm wire:click="openAskCancelModal({{ $reservation }})" />
                                    @endcan
                                @endif
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
        <x-front.pagination :pagination="$reservations" :per-page="$perPage" />
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

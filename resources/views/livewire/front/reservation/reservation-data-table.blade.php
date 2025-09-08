<div>
    <x-front.card>
        <div class="flex justify-between">
            <div class="text-2xl">{{ __("Bonjour") }} <span class="font-bold">{{ ucfirst(Auth::user()->prenom) }}</span></div>
            @can('create reservation')
                <x-button primary label="{{ __('Nouvelle réservation') }}" icon="plus" href="{{ route('front.reservation.create') }}" />
            @endcan
        </div>

    </x-front.card>

    <x-front.card>

        <x-front.title>
            {{ __('Historique des réservations') }}
        </x-front.title>

        <div class="relative">
            <x-datatable.search wire:model.live.debounce.300ms="search" />
            <div wire:loading wire:target="search" class="absolute right-2 top-8">
                <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <div wire:loading.class="opacity-50">
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
                                        {{ __("à confirmer") }}
                                    </x-front.badge>
                                    @break
                                @case(\App\Enum\ReservationStatus::Canceled)
                                    <x-front.badge danger>
                                        {{ __("Annulée") }}
                                    </x-front.badge>
                                    @break
                                @case(\App\Enum\ReservationStatus::CanceledToPay)
                                    <x-front.badge warning-secondary>
                                        {{ __("Annulée facturée") }}
                                    </x-front.badge>
                                    @break
                                @case(\App\Enum\ReservationStatus::Confirmed)
                                @case(\App\Enum\ReservationStatus::Billed)
                                    <x-front.badge success>
                                        {{ __("Confirmée") }}
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
                                        <x-mini-button rounded icon="pencil" info sm wire:click="openAskEditModal({{ $reservation }})" />
                                    @endcan
                                    @can('delete reservation')
                                        <x-mini-button rounded icon="x-mark" red sm wire:click="openAskCancelModal({{ $reservation }})" />
                                    @endcan
                                    @else
                                     <x-front.badge warning-secondary>
                                         Aucune action possible
                                     </x-front.badge>
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
        </div>
        <x-front.pagination :pagination="$reservations" :per-page="$perPage" />
    </x-front.card>

    <x-modal blur wire:model.defer="editAskCard">
        <x-card title="{{ __('Demande de modification') }}">
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
        </x-card>
    </x-modal>


    <x-modal blur wire:model.defer="askCancelCard">
        <x-card title="{{ __('Demande annulation') }}">
            @if($selectedReservation != null)
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ __("Attention") }} !</h3>
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
        </x-card>
    </x-modal>
</div>

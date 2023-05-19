<div>
    <x-header>
        Liste des courses du pilote <span class="text-blue-500">{{ $pilote->full_name }}</span>
        <x-slot:right>
            <x-button fllat label="Retour à la liste" href="{{ route('admin.pilotes.index') }}" sm />
        </x-slot:right>
    </x-header>
    <x-bloc-content>
        <div class="pb-3 border-b border-gray-200 dark:border-gray-600 sm:flex sm:items-center sm:justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Liste des réservations</h3>
            <div class="mt-3 sm:mt-0 sm:ml-4">
                <x-button label="Exporter" icon="download" sm primary wire:click="exportReservations" spinner="exportReservations" />
            </div>
        </div>
        <div class="py-3 grid grid-cols-3 gap-6">
            <x-datetime-picker
                without-time
                without-timezone
                label="Date de début"
                placeholder="Date de début"
                wire:model.defer="dateDebut"
                :clearable="false"
                display-format="DD/MM/YYYY"
            />
            <x-datetime-picker
                without-time
                without-timezone
                label="Date de fin"
                placeholder="Date de fin"
                wire:model.defer="dateFin"
                :clearable="false"
                display-format="DD/MM/YYYY"
            />
            <div class="flex items-end">
                <x-button label="Rechercher" primary wire:click="searchReservations" />
            </div>
        </div>
        <x-datatable>
            <x-slot name="headers">
                <tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Entreprise</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Départ</x-datatable.th>
                    <x-datatable.th>Arrivée</x-datatable.th>
                    <x-datatable.th>Passager</x-datatable.th>
                    <x-datatable.th>Validation</x-datatable.th>
                    <x-datatable.th>Encaisse</x-datatable.th>
                    <x-datatable.th>Encompte</x-datatable.th>
                    <x-datatable.th>Commentaire</x-datatable.th>
                    <x-datatable.th>Action</x-datatable.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @php
                    $validationAmount = 0;
                @endphp
                @forelse($reservations as $reservation)
                    @php
                        $validationAmount = $validationAmount + $reservation->totalTarifPilote();
                    @endphp
                    <x-datatable.tr :success="$reservation->totalTarifPilote() > 0"  x-data="formReservationPilote({{ json_encode($reservation) }})" wire:key="{{ $reservation->id }}">
                        <x-datatable.td>
                            <span class="text-blue-500" data-tooltip-target="tooltip-left{{ $reservation->id }}"
                                  data-tooltip-placement="top">{{ $reservation->reference }}</span>
                            <div id="tooltip-left{{ $reservation->id }}" role="tooltip"
                                 class="shadow-xl transition-opacity duration-300 inline-block absolute invisible z-10 py-2 px-3 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                                <dl>
                                    <dt>Départ</dt>
                                    <dd>{{ $reservation->display_from }}</dd>

                                    <div class="mt-3">
                                        <dt>Arrivée</dt>
                                        <dd>{{ $reservation->display_to }}</dd>
                                    </div>

                                    <div class="mt-2">
                                        <dt>Commentaire</dt>
                                        <dd>{{ $reservation->comment }}</dd>
                                    </div>
                                </dl>
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                        </x-datatable.td>
                        <x-datatable.td>{{ $reservation->entreprise->nom }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                        <x-datatable.td>
                            {{ $reservation->display_from }}
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ $reservation->display_to }}
                        </x-datatable.td>
                        <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                        <x-datatable.td>
                            <div class="text-left">
                                {{ number_format($validationAmount, 2, ',', ' ') }} €
                            </div>
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-input placeholder="Encaisse" x-model="formData.encaisse" value="{{ $reservation->encaisse_pilote }}"/>
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-input placeholder="Encompte" x-model="formData.encompte" value="{{ $reservation->encompte_pilote }}" />
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-textarea placeholder="Commentaire pour le pilote" x-model="formData.comment"/>
                        </x-datatable.td>
                        <x-datatable.td>
                            <input type="hidden">
                            <x-button label="Valider" primary sm @click="toto({{ $reservation->id }})" wire:loading.attr="disabled"/>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="11">Aucune réservation</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
            <x-slot name="tfoot">
            </x-slot>
        </x-datatable>
    </x-bloc-content>
    @push("scripts")
        <script>
            function formReservationPilote(reservation) {
                return {
                    formData : {
                        encaisse: reservation.encaisse_pilote,
                        encompte: reservation.encompte_pilote,
                        comment: reservation.comment_pilote,
                        reservation: ''
                    },
                    toto(reservationId) {
                        this.formData.reservation = reservationId
                        @this.emit('editReservation', this.formData)
                    }
                }
            }
        </script>
    @endpush
</div>

<div>
    <x-header>
        Liste des courses du pilote <span class="text-blue-500">{{ $pilote->full_name }}</span>
    </x-header>
    <x-bloc-content>
        <div class="pb-3 border-b border-gray-200 dark:border-gray-600 sm:flex sm:items-center sm:justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Liste des réservations</h3>
            <div class="mt-3 sm:mt-0 sm:ml-4">
                <x-button label="Exporter" icon="download" sm primary/>
            </div>
        </div>
        <div class="py-3 grid grid-cols-3 gap-6">
            <x-datetime-picker
                without-time
                without-timezone
                label="Date de début"
                placeholder="Date de début"
                wire:model="dateDebut"
            />
            <x-datetime-picker
                without-time
                without-timezone
                label="Date de fin"
                placeholder="Date de fin"
                wire:model="dateFin"
            />
        </div>
        <x-datatable>
            <x-slot name="headers">
                <tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Entreprise</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Client</x-datatable.th>
                    <x-datatable.th>Validation</x-datatable.th>
                    <x-datatable.th>Tarif</x-datatable.th>
                    <x-datatable.th>Majoration</x-datatable.th>
                    <x-datatable.th>Encaisse</x-datatable.th>
                    <x-datatable.th>Encompte</x-datatable.th>
                    <x-datatable.th>Action</x-datatable.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse($reservations as $reservation)
                    <x-datatable.tr>
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
                        <x-datatable.td>{{ $reservation->passager->user->entreprise->nom }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->passager->user->full_name }}</x-datatable.td>
                        <x-datatable.td>
                            XXXX €
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-input placeholder="Tarif" right-icon="currency-euro" form="form{{ $reservation->id }}"/>
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-input placeholder="Majoration en %"/>
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-input placeholder="Encaisse"/>
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-input placeholder="Encompte" right-icon="currency-euro"/>
                        </x-datatable.td>
                        <x-datatable.td>
                            <form action="" id="form{{ $reservation->id }}">
                                <x-button label="Valider" info sm/>
                            </form>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="10">Aucune réservation</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
            <x-slot name="tfoot">
            </x-slot>
        </x-datatable>
        <div class="mt-4 px-1">
            {{ $reservations->links('components.datatable.pagination') }}
        </div>
    </x-bloc-content>
</div>

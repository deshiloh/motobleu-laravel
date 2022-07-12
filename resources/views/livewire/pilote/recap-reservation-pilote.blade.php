<div>
    <x-title-section>
        <x-slot:title>
            Liste des courses du pilote <span class="text-blue-500">{{ $pilote->full_name }}</span>
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <div class="text-2xl">Liste des réservations</div>
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
            <div class="flex items-end">
                <button class="btn btn-primary gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exporter
                </button>
            </div>
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
    </x-admin.content>
</div>

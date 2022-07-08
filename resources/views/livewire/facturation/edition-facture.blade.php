@php
    $startedYear = \Carbon\Carbon::now()->subYears(4)->year;
    $endYear = \Carbon\Carbon::now()->addYears(4)->year;
@endphp
<div>
    <x-title-section>
        <x-slot:title>
            @if($this->entreprise)
                Édition de la facturation <span class="text-blue-500">{{ $this->entreprise->nom }}</span>
            @else
                Édition de la facturation
            @endif
        </x-slot:title>
        @if($entrepriseIdSelected)
            <x-button sm outline primary label="Retourner à la liste"
                      href="{{ route('admin.facturations.edition', [
                'selectedMonth' => $selectedMonth,
                '$selectedYear' => $selectedYear]
            ) }}"/>
        @endif
        @if($this->facture)
            <x-button label="Finaliser la facturation" sm green wire:click="sendFactureModal"/>
        @endif
    </x-title-section>
    @if(!$entrepriseIdSelected)
        <x-admin.content wire:key="entrepriseDataTable">
            <div class="border-b border-gray-400 pb-3 mb-4">
                <div class="grid grid-cols-4 gap-6">
                    <x-native-select
                        label="Mois"
                        placeholder="Sélectionner un mois"
                        wire:model="selectedMonth"
                    >
                        @foreach($months as $numMonth => $labelMonth)
                            <option value="{{ $numMonth }}">{{ $labelMonth }}</option>
                        @endforeach
                    </x-native-select>
                    <x-native-select
                        label="Année"
                        placeholder="Sélectionner une année"
                        wire:model="selectedYear"
                    >
                        @for($startedYear; $startedYear <= $endYear; $startedYear ++)
                            <option value="{{ $startedYear }}">{{ $startedYear }}</option>
                        @endfor
                    </x-native-select>
                </div>
            </div>
            <div class="text-2xl">
                Liste des entreprises à facturer
            </div>
            <x-datatable>
                <x-slot:headers>
                    <x-datatable.tr>
                        <x-datatable.th>Entreprise</x-datatable.th>
                        <x-datatable.th>Réservations</x-datatable.th>
                        <x-datatable.th>Actions</x-datatable.th>
                    </x-datatable.tr>
                </x-slot:headers>
                <x-slot:body>
                    @forelse($this->entreprises as $entreprise)
                        <x-datatable.tr>
                            <x-datatable.td>{{ $entreprise->nom }}</x-datatable.td>
                            <x-datatable.td>{{ $entreprise->nbReservations }}</x-datatable.td>
                            <x-datatable.td>
                                <x-button label="Éditer la facturation" info sm
                                          wire:click="goToEditPage('{{ $entreprise->id }}')"/>
                            </x-datatable.td>
                        </x-datatable.tr>
                    @empty
                        <x-datatable.tr>
                            <x-datatable.td colspan="3" class="text-center">
                                Aucune entreprise
                            </x-datatable.td>
                        </x-datatable.tr>
                    @endforelse
                </x-slot:body>
            </x-datatable>
        </x-admin.content>
    @endif
    @if($this->reservations)
        <x-admin.content>
            <div class="text-2xl mb-3   ">Liste des réservations</div>
            <x-datatable>
                <x-slot:headers>
                    <x-datatable.tr>
                        <x-datatable.th>Référence</x-datatable.th>
                        <x-datatable.th>Date</x-datatable.th>
                        <x-datatable.th>Passager</x-datatable.th>
                        <x-datatable.th>Départ</x-datatable.th>
                        <x-datatable.th>Arrivée</x-datatable.th>
                        <x-datatable.th>Montant total</x-datatable.th>
                        <x-datatable.th>Actions</x-datatable.th>
                    </x-datatable.tr>
                </x-slot:headers>
                <x-slot:body>
                    @foreach($this->reservations as $reservation)
                        <x-datatable.tr>
                            <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                            <x-datatable.td> 0 € </x-datatable.td>
                            <x-datatable.td>
                                <x-button label="Éditer" info sm wire:click="reservationModal('{{ $reservation->id }}')" />
                            </x-datatable.td>
                        </x-datatable.tr>
                    @endforeach
                </x-slot:body>
            </x-datatable>
            <div class="flex justify-end py-4">
                <div class="flex flex-col space-y-3 text-right">
                    <div>
                        <strong>Montant H.T :</strong>  00.00 €
                    </div>
                    <div>
                        <strong>TVA 10% :</strong> 00.00 €
                    </div>
                    <div>
                        <strong>Montant TTC :</strong> 00.00 €
                    </div>
                </div>
            </div>
        </x-admin.content>
    @endif
    @if($this->facture)
        <x-modal wire:model.defer="factureModal">
            <x-card title="Envoi de la facture">
                <x-errors class="mb-4"/>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        PDF
                    </div>
                    <div>
                        <form wire:submit.prevent="sendFactureAction" id="factureForm" class="space-y-4">
                            <x-input label="Email" wire:model.defer="email.address"/>
                            <x-tinymce wire:model="email.message"/>
                            <x-toggle label="Facture acquittée" lg wire:model.defer="isAcquitte"/>
                            <x-textarea label="Texte information" hint="Ce texte apparaitra sur la facture"/>
                        </form>
                    </div>
                </div>
                <x-slot name="footer">
                    <div class="flex justify-end gap-x-4">
                        <x-button flat label="Cancel" x-on:click="close"/>
                        <x-button info label="Finaliser et envoyer" type="submit" form="factureForm"/>
                    </div>
                </x-slot>
            </x-card>
        </x-modal>
    @endif
    @if($this->reservation)
        <x-modal wire:model.defer="reservationModal">
            <x-card title="Valeur de la réservation {{ $this->reservation->reference }}">
                <x-errors class="mb-4"/>
                <form id="reservationForm" class="space-y-4" wire:submit.prevent="saveReservationAction">
                    <x-input label="Prix" wire:model.defer="reservationFormData.tarif"/>
                    <x-input label="Majoration" wire:model.defer="reservationFormData.majoration"/>
                    <x-input label="Complément" wire:model.defer="reservationFormData.complement"/>
                    <x-textarea label="Message pour le pilote" wire:model.defer="reservationFormData.comment_pilote"/>
                </form>
                <x-slot name="footer">
                    <div class="flex justify-end gap-x-4">
                        <x-button flat label="Cancel" x-on:click="close"/>
                        <x-button info label="Valider" type="submit" form="reservationForm"/>
                    </div>
                </x-slot>
            </x-card>
        </x-modal>
    @endif
</div>

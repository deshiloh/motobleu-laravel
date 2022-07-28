@php
    $startedYear = \Carbon\Carbon::now()->subYears(4)->year;
    $endYear = \Carbon\Carbon::now()->addYears(4)->year;
@endphp
<div>
    <x-header wire:key="header">
        Édition de la facturation
    </x-header>
    @if(!$entrepriseIdSelected)
        <x-bloc-content wire:key="entrepriseDataTable">
            <div class="border-b border-gray-200 pb-3 mb-4">
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
            <div class="text-2xl mb-4 text-black dark:text-gray-200">
                Liste des entreprises à facturer
            </div>
            <x-datatable>
                <x-slot:headers>
                    <tr>
                        <x-datatable.th>Entreprise</x-datatable.th>
                        <x-datatable.th>Réservations</x-datatable.th>
                        <x-datatable.th>Actions</x-datatable.th>
                    </tr>
                </x-slot:headers>
                <x-slot:body>
                    @forelse($this->entreprises as $entreprise)
                        <x-datatable.tr>
                            <x-datatable.td>{{ $entreprise->nom }}</x-datatable.td>
                            <x-datatable.td>{{ $entreprise->nbReservations }}</x-datatable.td>
                            <x-datatable.td>
                                <x-button primary sm wire:click="goToEditPage('{{ $entreprise->id }}')" label="Éditer la facturation" />
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
        </x-bloc-content>
    @endif
    @if($this->reservations)
        <x-bloc-content wire:key="headerbis">
            <div class="flex items-center">
                <div class="text-xl">
                    @if($this->entreprise)
                        Édition de la facturation <span class="text-blue-500">{{ $this->entreprise->nom }}</span>
                    @endif
                </div>
                <div class="ml-auto">
                    @if($entrepriseIdSelected)
                        <x-button href="{{ route('admin.facturations.edition', [
                            'selectedMonth' => $selectedMonth,
                            '$selectedYear' => $selectedYear]
                        ) }}" label="Retourner à la liste" sm />
                    @endif
                    @if(!$this->adresseFacturationEntreprise && $entrepriseIdSelected)
                        <div class="p-2 text-sm text-yellow-700 bg-yellow-100 rounded-lg dark:bg-yellow-200 dark:text-yellow-800" role="alert">
                            <span class="font-medium">Attention</span> L'entreprise n'as pas d'adresse de facturation
                        </div>
                    @endif
                    @if($this->facture && $this->adresseFacturationEntreprise)
                        <x-button wire:click="sendFactureModal" label="Finaliser la facturation" positive sm />
                    @endif
                </div>
            </div>
        </x-bloc-content>
        <x-bloc-content>
            <div class="text-2xl mb-3">Liste des réservations</div>
            <x-datatable>
                <x-slot:headers>
                    <tr>
                        <x-datatable.th>Référence</x-datatable.th>
                        <x-datatable.th>Date</x-datatable.th>
                        <x-datatable.th>Passager</x-datatable.th>
                        <x-datatable.th>Départ</x-datatable.th>
                        <x-datatable.th>Arrivée</x-datatable.th>
                        <x-datatable.th>Montant total</x-datatable.th>
                        <x-datatable.th>Actions</x-datatable.th>
                    </tr>
                </x-slot:headers>
                <x-slot:body>
                    @foreach($this->reservations as $reservation)
                        @php
                            $currentAmount = 0;
                            $currentAmount = $this->calculTotal($reservation);
                            $montant_ttc += $currentAmount;
                        @endphp
                        <x-datatable.tr :success="$currentAmount > 0">
                            <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                            <x-datatable.td> {{ number_format($currentAmount, 2, ',', ' ') }} € </x-datatable.td>
                            <x-datatable.td>
                                <x-button primary sm wire:click="reservationModal('{{ $reservation->id }}')" label="Éditer" />
                            </x-datatable.td>
                        </x-datatable.tr>
                    @endforeach
                </x-slot:body>
            </x-datatable>
            <div class="flex justify-end py-4">
                <div class="flex flex-col space-y-3 text-right">
                    @php
                        $prixHT = $montant_ttc / 1.10;
                        $prixTVA = $prixHT * 0.10;
                    @endphp
                    <div>
                        <strong>Montant H.T :</strong> {{ number_format($prixHT, 2, ',', ' ') }} €
                    </div>
                    <div>
                        <strong>TVA 10% :</strong> {{ number_format($prixTVA, 2, ',', ' ') }} €
                    </div>
                    <div>
                        <strong>Montant TTC :</strong> {{ number_format($montant_ttc, 2, ',', ' ') }} €
                    </div>
                </div>
            </div>
        </x-bloc-content>
    @endif
    @if($this->facture)
        <x-modal wire:model.defer="factureModal">
            <x-card title="Envoi de la facture" wire:key="facture">
                <x-errors class="mb-4"/>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <iframe src="/admin/facturations/1/show?uniq={{ $uniqID }}#view=FitH&toolbar=1" class="w-full h-full"></iframe>
                    </div>
                    <div>
                        <form wire:submit.prevent="sendFactureAction" id="factureForm" class="space-y-4">
                            <x-input label="Email" wire:model.defer="email.address"/>
                            <x-tinymce wire:model="email.message"/>
                            <x-toggle wire:model.defer="isAcquitte" wire:change="editFactureAction" label="Facture acquittée" md />
                            <x-textarea label="Texte information" hint="Ce texte apparaitra sur la facture" wire:model.defer="email.complement" wire:change.debounce="editFactureAction"/>
                            <x-button wire:click="sendEmailTestAction" primary sm type="button">Envoi d'un email de test</x-button>
                        </form>
                    </div>
                </div>
                <x-slot name="footer">
                    <div class="flex justify-end gap-x-4">
                        <x-button x-on:click="close" sm >
                            Annuler
                        </x-button>
                        <x-button type="submit" form="factureForm" primary sm >
                            Finaliser et envoyer
                        </x-button>
                    </div>
                </x-slot>
            </x-card>
        </x-modal>
    @endif
    @if($this->reservation)
        <x-modal wire:model.defer="reservationModal" blur wire:key="reservation">
            <x-card title="Valeur de la réservation {{ $this->reservation->reference }}">
                <x-errors class="mb-4"/>
                <form id="reservationForm" class="space-y-4" wire:submit.prevent="saveReservationAction">
                    <x-input label="Prix" wire:model.defer="reservationFormData.tarif" type="number" step="0.01"/>
                    <x-input label="Majoration" wire:model.defer="reservationFormData.majoration" type="number" step="0.01"/>
                    <x-input label="Complément" wire:model.defer="reservationFormData.complement" type="number" step="0.01"/>
                    <x-textarea label="Message pour le pilote" wire:model.defer="reservationFormData.comment_pilote"/>
                </form>
                <x-slot name="footer">
                    <div class="flex justify-end gap-x-4">
                        <x-button sm x-on:click="close">
                            Annuler
                        </x-button>
                        <x-button type="submit" form="reservationForm" primary sm >
                            Valider
                        </x-button>
                    </div>
                </x-slot>
            </x-card>
        </x-modal>
    @endif
</div>

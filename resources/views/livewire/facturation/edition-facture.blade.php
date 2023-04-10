@php
    $startedYear = \Carbon\Carbon::now()->subYears(4)->year;
    $endYear = \Carbon\Carbon::now()->addYears(4)->year;
@endphp
<div>
    <x-header wire:key="header">
        @if($this->entreprise)
            Édition de la facturation <span class="text-blue-500">{{ $this->entreprise->nom }}</span>
            <x-slot:right>
                <div>
                    @if($entrepriseIdSelected && !$this->isBilled == 1)
                        <x-button href="{!! route('admin.facturations.edition', [
                                'selectedMonth' => $selectedMonth,
                                'selectedYear' => $selectedYear,
                            ]
                        ) !!}" label="Retourner à la liste" sm />
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
            </x-slot:right>
        @else
            Édition de la facturation
        @endif
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
                    <x-select
                        label="Entreprise"
                        placeholder="Sélectionner une entreprise"
                        :async-data="route('api.entreprises')"
                        option-label="nom"
                        option-value="id"
                        wire:model="entrepriseSearch"
                    />
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
                            <x-datatable.td>{{ $entreprise->reservations_count }}</x-datatable.td>
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
        <x-bloc-content>
            <div class="text-2xl mb-3 dark:text-white">Liste des réservations</div>
            <x-datatable>
                <x-slot:headers>
                    <tr>
                        <x-datatable.th>Référence</x-datatable.th>
                        <x-datatable.th>Date</x-datatable.th>
                        <x-datatable.th>Passager</x-datatable.th>
                        <x-datatable.th>Départ</x-datatable.th>
                        <x-datatable.th>Arrivée</x-datatable.th>
                        <x-datatable.th>Validation (€)</x-datatable.th>
                        <x-datatable.th>Tarif (€)</x-datatable.th>
                        <x-datatable.th>Majoration (%)</x-datatable.th>
                        <x-datatable.th>Compléments (€)</x-datatable.th>
                        <x-datatable.th>Commentaire</x-datatable.th>
                        <x-datatable.th>Actions</x-datatable.th>
                    </tr>
                </x-slot:headers>
                <x-slot:body>
                    @php
                        $validation = 0;
                    @endphp
                    @foreach($this->reservations as $reservation)
                        @php
                            $currentAmount = 0;
                            $currentAmount = $this->calculTotal($reservation);
                            $montant_ttc += $currentAmount;
                            $validation += $this->calculTotal($reservation);
                        @endphp
                        <x-datatable.tr :success="$currentAmount > 0" x-data="billDatas({{ json_encode($reservation) }})">
                            <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                            <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                            <x-datatable.td>
                                @if($reservation->tarif > 0)
                                    {{ number_format($validation, 2, ',', ' ') }} €
                                @endif
                            </x-datatable.td>
                            <x-datatable.td>
                                <x-input x-model="formData.tarif" type="number" step="0.01" placeholder="Tarif de la course"/>
                            </x-datatable.td>
                            <x-datatable.td>
                                <x-input type="number" step="0.01" x-model="formData.majoration" placeholder="Majoration de la course"/>
                            </x-datatable.td>
                            <x-datatable.td>
                                <x-input x-model="formData.complement" type="number" step="0.01" placeholder="Complément de la course"/>
                            </x-datatable.td>
                            <x-datatable.td>
                                <x-textarea x-model="formData.comment_facture" placeholder="Votre commentaire" />
                            </x-datatable.td>
                            <x-datatable.td>
                                <x-button primary sm label="Valider" @click="submission"/>
                            </x-datatable.td>
                        </x-datatable.tr>
                    @endforeach
                </x-slot:body>
            </x-datatable>
            <div class="flex justify-end py-4">
                <div class="flex flex-col space-y-3 text-right dark:text-white">
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

    <x-modal wire:model.defer="factureModal" max-width="6xl">
        @if($this->facture)
        <x-card title="Envoi de la facture" wire:key="facture">
            <x-errors class="mb-4"/>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <iframe src="/admin/facturations/{{ $this->facture->id }}/show?uniq={{ $uniqID }}#view=FitH&toolbar=1" class="w-full h-full"></iframe>
                </div>
                <div>
                    <form wire:submit.prevent="sendFactureAction" id="factureForm" class="space-y-4">
                        <x-input label="Email" wire:model.defer="email.address"/>
                        <x-tinymce wire:model="email.message"/>
                        <x-toggle wire:model.defer="isAcquitte" wire:change="editFactureAction" label="Facture acquittée" md />
                        <x-textarea label="Texte information" hint="Ce texte apparaitra sur la facture" wire:model.defer="email.complement" wire:change.debounce="editFactureAction"/>
                        <x-button wire:click="sendEmailTestAction" primary sm type="button" icon="mail">Envoi d'un email de test</x-button>
                        <x-button wire:click="exportAction" info sm type="button" icon="download">Récap. des courses</x-button>
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
        @endif
    </x-modal>

    @push('scripts')
        <script>
            function billDatas(reservationData) {
                return {
                    formData : {
                        tarif : reservationData.tarif,
                        majoration: reservationData.majoration,
                        complement: reservationData.complement,
                        comment_facture: reservationData.comment_facture,
                        reservation: reservationData.id
                    },
                    submission() {
                        @this.emit('editReservation', this.formData)
                    }
                }
            }
        </script>
    @endpush
</div>

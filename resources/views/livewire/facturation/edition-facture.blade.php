@php
    $months = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre',
 ]
@endphp
<div>
    <x-title-section>
        <x-slot:title>
            @if($this->entreprise && $month && $year)
                Édition de facturation pour l'entrerise <span class="text-blue-500">{{ $this->entreprise->nom }}</span>
            @else
                Édition de facturation
            @endif
        </x-slot:title>

        @if(!$this->entreprise || !$this->month || !$this->year)
            <span
                class="text-sm">Sélectionner une entreprise avec le mois et l'année pour finaliser la facturation</span>
        @endif

        @if($this->entrepriseId && !$this->adresseFacturationEntreprise)
            <div
                class="px-4 py-2 text-sm text-yellow-700 bg-yellow-100 rounded-lg dark:bg-yellow-200 dark:text-yellow-800"
                role="alert">
                <span class="font-bold">Attention !</span> L'entreprise n'as pas d'adresse de facturation : <a
                    class="underline"
                    href="{{ route('admin.entreprises.show', ['entreprise' => $this->entrepriseId]) }}">voir les
                    adresses</a>
            </div>
        @endif

        @if($this->entreprise && $month && $year && $this->adresseFacturationEntreprise && $this->reservations->count() > 0)
            <x-button label="Finaliser la facturation" positive wire:click="$set('madeBillModal', true)"/>
        @endif
    </x-title-section>
    <x-admin.content>
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <x-native-select label="Mois" wire:model="month" wire:change="$set('entrepriseId', null)">
                    @foreach($months as $monthNumber => $monthLabel)
                        <option value="{{ $monthNumber }}">{{ $monthLabel }}</option>
                    @endforeach
                </x-native-select>
            </div>
            <div>
                <x-native-select label="Année" wire:model="year" wire:change="$set('entrepriseId', null)">
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                </x-native-select>
            </div>
            <div>
                <x-select
                    label="Entreprise"
                    wire:model="entrepriseId"
                    placeholder="Sélectionner une entreprise"
                    :async-data="route('api.entreprises.bill', [
                        'year' => $year,
                        'month' => $month
                    ])"
                    option-label="nom"
                    option-value="id"
                />
            </div>
        </div>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Passager</x-datatable.th>
                    <x-datatable.th>Départ</x-datatable.th>
                    <x-datatable.th>Arrivée</x-datatable.th>
                    <x-datatable.th>Total</x-datatable.th>
                    <x-datatable.th>Commentaire</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($this->reservations as $reservation)
                    @php
                        $total = $this->calculTotal($reservation);
                    @endphp
                    <x-datatable.tr :success="$total > 0">
                        <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
                        <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
                        <x-datatable.td>
                            {{ $total }} €
                        </x-datatable.td>
                        <x-datatable.td>
                            {{ $reservation->comment_pilote }}
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-button label="Éditer" info sm wire:click="editItem('{{ $reservation->id }}')"/>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="8" class="text-center">Aucune réservation</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </x-admin.content>
    <x-modal wire:model.defer="simpleModal" blur>
        @if($currentReservation)
            <x-card title="Édition de la réservation {{ $currentReservation->reference }}">
                <form wire:submit.prevent="saveItem" id="factureSaveForm">
                    <div class="space-y-3">
                        <x-input placeholder="Prix" right-icon="currency-euro"
                                 wire:model.defer="currentReservation.tarif" label="Prix"/>
                        <x-input placeholder="Majoration en %" wire:model.defer="currentReservation.majoration"
                                 label="Majoration"/>
                        <x-input placeholder="Complément" right-icon="currency-euro"
                                 wire:model.defer="currentReservation.complement" label="Complément"/>
                        <x-textarea placeholder="Commentaire pour le pilote"
                                    wire:model.defer="currentReservation.comment_pilote" label="Commentaire"/>
                    </div>

                    <x-slot name="footer">
                        <div class="flex justify-end gap-x-4">
                            <x-button flat label="Annuler" x-on:click="close"/>
                            <x-button info label="Enregistrer" type="submit" form="factureSaveForm"/>
                        </div>
                    </x-slot>
                </form>
            </x-card>
        @endif
    </x-modal>
    <x-modal wire:model.defer="madeBillModal" blur>
        @if($this->entreprise && $this->adresseFacturationEntreprise)
            <x-card title="Finalisation de la facture de {{ $this->entreprise->nom }}">
                <div class="mb-4">
                    <x-errors />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        PDF
                    </div>
                    <div>
                        <form id="finalFactureForm" wire:submit.prevent="generateFacture">
                            <div class="space-y-4">
                                <x-input label="Destinataire" wire:model.defer="email.adresseTo"/>
                                <x-textarea label="Message" wire:model.defer="email.message" />
                                <x-toggle label="Facture acquittée" wire:model.defer="facture.is_acquitte"/>
                                <x-textarea label="Information" hint="Ce texte apparaitra sur la facture"/>
                            </div>
                        </form>
                    </div>
                </div>
                <x-slot name="footer">
                    <div class="flex justify-end gap-x-4">
                        <x-button flat label="Annuler" x-on:click="close"/>
                        <x-button info label="Finaliser et envoyer" type="submit" form="finalFactureForm"/>
                    </div>
                </x-slot>
            </x-card>
        @endif
    </x-modal>
</div>

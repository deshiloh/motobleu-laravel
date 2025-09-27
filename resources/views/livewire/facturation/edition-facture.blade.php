<div>
    <x-dialog />
    <x-header wire:key="header">
        @if($this->facture)
            Édition de la facture <span class="text-blue-500">{{ $this->entreprise?->nom }}</span>
            <x-slot:right>
                <x-facturation.header-actions
                    :facture="$facture"
                    :selectedMonth="$selectedMonth"
                    :selectedYear="$selectedYear"
                    :entreprise="$this->entreprise"
                />
            </x-slot:right>
        @else
            Édition de la facturation
        @endif
    </x-header>

    @if(!$facture)
        @include('livewire.facturation.partials.companies-list', [
            'entreprises' => $this->entreprises,
            'months' => $months,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'entrepriseSearch' => $entrepriseSearch
        ])
    @endif

    @if($facture)
        @include('livewire.facturation.partials.invoice-info', [
            'facture' => $this->facture,
            'isAcquitte' => $isAcquitte
        ])

        @include('livewire.facturation.partials.reservations-list', [
            'facture' => $facture
        ])
    @endif

    @include('livewire.facturation.partials.send-invoice-modal', [
        'facture' => $facture,
        'uniqID' => $uniqID,
        'email' => $email
    ])

    @include('livewire.facturation.partials.scripts')
</div>

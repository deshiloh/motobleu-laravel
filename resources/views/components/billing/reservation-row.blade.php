@props(['reservation', 'validation'])

<x-datatable.tr :success="$reservation->tarif !== null" x-data="billDatas({{ json_encode($reservation) }})">
    <x-datatable.td>{{ $reservation->reference }}</x-datatable.td>
    <x-datatable.td>{{ $reservation->pickup_date->format('d/m/Y H:i') }}</x-datatable.td>
    <x-datatable.td>{{ $reservation->passager->nom }}</x-datatable.td>
    <x-datatable.td>{{ $reservation->display_from }}</x-datatable.td>
    <x-datatable.td>{{ $reservation->display_to }}</x-datatable.td>
    <x-datatable.td>
        @if($reservation->tarif !== null)
            <x-billing.amount-display :amount="$validation" />
        @endif
    </x-datatable.td>
    <x-datatable.td>
        <div class="w-24 xl:w-full">
            <x-input x-model="formData.tarif" type="number" step="0.01" placeholder="Tarif de la course" />
        </div>
    </x-datatable.td>
    <x-datatable.td>
        <div class="w-24 xl:w-full">
            <x-input type="number" step="0.01" x-model="formData.majoration" placeholder="Majoration de la course"/>
        </div>
    </x-datatable.td>
    <x-datatable.td>
        <div class="w-24 xl:w-full">
            <x-input x-model="formData.complement" type="number" step="0.01" placeholder="Complément de la course"/>
        </div>
    </x-datatable.td>
    <x-datatable.td>
        <x-textarea x-model="formData.comment_facture" placeholder="Votre commentaire" />
    </x-datatable.td>
    <x-datatable.td>
        <x-button primary sm label="Valider" @click="submission"/>
    </x-datatable.td>
</x-datatable.tr>
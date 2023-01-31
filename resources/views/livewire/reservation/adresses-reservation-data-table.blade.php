<div>
    <x-datatable.search wire:model="search" />
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th>Adresse</x-datatable.th>
                <x-datatable.th>Code postal</x-datatable.th>
                <x-datatable.th>Ville</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($adresses as $adresse)
                <x-datatable.tr>
                    <x-datatable.td>{{ $adresse->adresse }}</x-datatable.td>
                    <x-datatable.td>{{ $adresse->code_postal }}</x-datatable.td>
                    <x-datatable.td>{{ $adresse->ville }}</x-datatable.td>
                    <x-datatable.td>
                        <x-button.circle info icon="pencil" href="{{ route('admin.adresse-reservation.edit', ['adresseReservation' => $adresse->id]) }}" />
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <tr>
                    <x-datatable.td class="text-center bg-gray-700" colspan="5">
                        Aucune adresse de réservation
                    </x-datatable.td>
                </tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <x-front.pagination :pagination="$adresses" :per-page="$perPage"/>
</div>

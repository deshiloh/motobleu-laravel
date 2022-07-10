<div>
    <x-admin.content>
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
                            <div class="flex space-x-2">
                                <x-actions.edit href="{{ route('admin.adresse-reservation.edit', ['adresseReservation' => $adresse->id]) }}" />
                            </div>
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
            <x-slot:footer>
                <x-datatable.tr>
                    <x-datatable.th colspan="4">
                        {{ $adresses->links('components.datatable.pagination') }}
                    </x-datatable.th>
                </x-datatable.tr>
            </x-slot:footer>
        </x-datatable>
    </x-admin.content>
</div>

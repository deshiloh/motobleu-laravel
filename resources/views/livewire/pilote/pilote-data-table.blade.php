<div>
    <x-datatable.search wire:model="search"/>
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('prenom')" :direction="$sortDirection">Prénom
                </x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('email')" :direction="$sortDirection">Email
                </x-datatable.th>
                <x-datatable.th>Téléphone</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('adresse')" :direction="$sortDirection">Adresse
                </x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($pilotes as $pilote)
                <x-datatable.tr>
                    <x-datatable.td>{{ $pilote->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $pilote->prenom }}</x-datatable.td>
                    <x-datatable.td>{{ $pilote->email }}</x-datatable.td>
                    <x-datatable.td>{{ $pilote->telephone }}</x-datatable.td>
                    <x-datatable.td>{{ $pilote->adresse }}</x-datatable.td>
                    <x-datatable.td>
                        <x-button.circle href="{{ route('admin.pilotes.edit', ['pilote' => $pilote->id]) }}" info sm icon="pencil" />
                        <x-button.circle href="{{ route('admin.pilotes.recap-reservation', ['pilote' => $pilote->id]) }}" primary sm icon="view-list" />
                        <x-button.circle href="{{ route('admin.pilotes.destroy', ['pilote' => $pilote->id]) }}" red sm icon="trash" />
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="6">Aucun pilote</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <div class="py-4 px-3">
        {{ $pilotes->links('components.datatable.pagination') }}
    </div>
</div>

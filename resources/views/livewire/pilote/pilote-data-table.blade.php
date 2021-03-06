<div>
    <x-admin.content>
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
                            <x-dropdown>
                                <x-slot name="trigger">
                                    <x-button label="Actions" info sm/>
                                </x-slot>

                                <x-dropdown.item label="Éditer"
                                                 href="{{ route('admin.pilotes.edit', ['pilote' => $pilote->id]) }}"/>
                                <x-dropdown.item label="Récap. des courses"
                                                 href="{{ route('admin.pilotes.recap-reservation', ['pilote' => $pilote->id]) }}"/>
                                <x-dropdown.item separator
                                                 href="{{ route('admin.pilotes.destroy', ['pilote' => $pilote->id]) }}">
                                    <div class="text-red-500">Supprimer</div>
                                </x-dropdown.item>
                            </x-dropdown>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="6">Aucun pilote</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
            <x-slot name="tfoot">
            </x-slot>
        </x-datatable>
        <div class="mt-4 px-1">
            {{ $pilotes->links('components.datatable.pagination') }}
        </div>
    </x-admin.content>
</div>

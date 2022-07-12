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
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn m-1 btn-sm btn-primary">Actions</label>
                                <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a href="{{ route('admin.pilotes.edit', ['pilote' => $pilote->id]) }}">Éditer</a></li>
                                    <li><a href="{{ route('admin.pilotes.recap-reservation', ['pilote' => $pilote->id]) }}">Récap. des courses</a></li>
                                    <li><a class="text-error" href="{{ route('admin.pilotes.destroy', ['pilote' => $pilote->id]) }}">Supprimer</a></li>
                                </ul>
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="6">Aucun pilote</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
            <x-slot:footer>
                <x-datatable.tr>
                    <x-datatable.th colspan="6">
                        {{ $pilotes->links('components.datatable.pagination') }}
                    </x-datatable.th>
                </x-datatable.tr>
            </x-slot:footer>
        </x-datatable>
    </x-admin.content>
</div>

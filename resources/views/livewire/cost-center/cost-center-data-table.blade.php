<div>
    <x-admin.content>
        <x-datatable.search wire:model="search"/>
        <x-datatable>
            <x-slot name="headers">
                <tr>
                    <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                    <x-datatable.th sortable wire:click="sortBy('is_actif')" :direction="$sortDirection">Actif
                    </x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse($costcenters as $costcenter)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $costcenter->nom }}</x-datatable.td>
                        <x-datatable.td>
                            <x-badge :success="$costcenter->is_actif"></x-badge>
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="flex space-x-2">
                                <x-actions.edit
                                    href="{{ route('admin.costcenter.edit', ['costCenter' => $costcenter->id]) }}"/>
                                <x-actions.trash
                                    route="{{ route('admin.costcenter.destroy', ['costcenter' => $costcenter->id]) }}"/>
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="5">Aucun Cost Center</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
            <x-slot:footer>
                <x-datatable.tr>
                    <x-datatable.th colspan="3">
                        {{ $costcenters->links('components.datatable.pagination') }}
                    </x-datatable.th>
                </x-datatable.tr>
            </x-slot:footer>
        </x-datatable>
    </x-admin.content>
</div>

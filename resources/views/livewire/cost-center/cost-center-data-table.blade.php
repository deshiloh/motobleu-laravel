<div>
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
                            <x-button.circle icon="pencil" info href="{{ route('admin.costcenter.edit', ['costCenter' => $costcenter->id]) }}" />
                            <x-button.circle icon="trash" red route="{{ route('admin.costcenter.destroy', ['costcenter' => $costcenter->id]) }}" />
                         </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="5">Aucun Cost Center</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <div class="px-3 py-4">
        {{ $costcenters->links('components.datatable.pagination') }}
    </div>
</div>

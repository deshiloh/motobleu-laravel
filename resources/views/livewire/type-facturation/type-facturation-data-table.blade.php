<div>
    <x-admin.content>
        <x-datatable.search wire:model="search"/>
        <x-datatable>
            <x-slot name="headers">
                <tr>
                    <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse($typefacturations as $typefacturation)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $typefacturation->nom }}</x-datatable.td>
                        <x-datatable.td>
                            <div class="flex space-x-2">
                                <x-actions.edit
                                    href="{{ route('admin.typefacturation.edit',['typefacturation' => $typefacturation]) }}"/>
                                <x-actions.trash
                                    route="{{ route('admin.typefacturation.destroy', ['typefacturation' => $typefacturation->id]) }}"/>
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="5">Aucun type de facturation</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
            <x-slot name="tfoot">
            </x-slot>
        </x-datatable>
        <div class="mt-4 px-1">
            {{ $typefacturations->links('components.datatable.pagination') }}
        </div>
    </x-admin.content>
</div>

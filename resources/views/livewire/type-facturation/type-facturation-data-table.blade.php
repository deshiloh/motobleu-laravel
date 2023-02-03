<div>
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
                            <x-button.circle icon="pencil" info href="{{ route('admin.typefacturation.edit',['typefacturation' => $typefacturation]) }}" />
                            <x-button.circle icon="trash" red route="{{ route('admin.typefacturation.destroy', ['typefacturation' => $typefacturation->id]) }}" />
                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="5">Aucun type de facturation</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <x-front.pagination :pagination="$typefacturations" :per-page="$perPage"/>
</div>

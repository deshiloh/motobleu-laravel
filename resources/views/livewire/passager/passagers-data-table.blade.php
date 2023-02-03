<div>
    <x-datatable.search wire:model="search" />
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                <x-datatable.th>Secrétaire</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($passagers as $passager)
                <x-datatable.tr>
                    <x-datatable.td>{{ $passager->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $passager->user->full_name }}</x-datatable.td>
                    <x-datatable.td>
                        <div class="flex space-x-2">
                            <x-button.circle icon="pencil" info href="{{ route('admin.passagers.edit', ['passager' => $passager->id]) }}" />
                            <x-button.circle icon="trash" red route="{{ route('admin.passagers.destroy', ['passager' => $passager->id]) }}" />
                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="5">Aucun passager</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <x-front.pagination :pagination="$passagers" :per-page="$perPage"/>
</div>

<div>
    <x-datatable.search wire:model="search" />
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                <x-datatable.th>Actif</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($localisations as $localisation)
                <x-datatable.tr>
                    <x-datatable.td>{{ $localisation->nom }}</x-datatable.td>
                    <x-datatable.td>
                        @if($localisation->is_actif)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"> Oui </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"> Non </span>
                        @endif
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="flex space-x-2">
                            <x-button.circle icon="pencil" info href="{{ route('admin.localisations.edit', ['localisation' => $localisation->id]) }}" />
                            <x-button.circle icon="trash" red route="{{ route('admin.localisations.destroy', ['localisation' => $localisation->id]) }}" />
                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="5">Aucune localisation</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <div class="pt-4 px-3">
        {{ $localisations->links('components.datatable.pagination') }}
    </div>
</div>

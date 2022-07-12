<div>
    <x-admin.content>
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
                            <x-badge :success="$localisation->is_actif"></x-badge>
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="flex space-x-2">
                                <x-actions.edit href="{{ route('admin.localisations.edit', ['localisation' => $localisation->id]) }}" />
                                <x-actions.trash route="{{ route('admin.localisations.destroy', ['localisation' => $localisation->id]) }}" />
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="5">Aucune localisation</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
            <x-slot:footer>
                <x-datatable.tr>
                    <x-datatable.th colspan="3">
                        {{ $localisations->links('components.datatable.pagination') }}
                    </x-datatable.th>
                </x-datatable.tr>
            </x-slot:footer>
        </x-datatable>
    </x-admin.content>
</div>

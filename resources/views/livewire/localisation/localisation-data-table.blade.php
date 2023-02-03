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
                        <x-front.badge :success="$localisation->is_actif" :danger="!$localisation->is_actif">
                            {{ $localisation->is_actif ? "Oui" : "Non" }}
                        </x-front.badge>
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
    <x-front.pagination :pagination="$localisations" :per-page="$perPage"/>
</div>

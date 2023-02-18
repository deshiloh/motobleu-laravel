<div>
    <x-datatable.search wire:model="search" />
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('is_actif')" :direction="$sortDirection">Actif</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($entreprises as $entreprise)
                <x-datatable.tr>
                    <x-datatable.td>{{ $entreprise->nom }}</x-datatable.td>
                    <x-datatable.td>
                        <x-front.badge :success="$entreprise->is_actif" :danger="!$entreprise->is_actif">
                            {{ $entreprise->is_actif ? "Oui" : "Non" }}
                        </x-front.badge>
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="flex space-x-2">
                            <x-button.circle icon="eye" info href="{{ route('admin.entreprises.show', ['entreprise' => $entreprise->id]) }}" />
                            <x-button.circle icon="pencil" primary href="{{ route('admin.entreprises.edit', ['entreprise' => $entreprise->id]) }}" />
                            @if($entreprise->is_actif)
                                <x-button.circle icon="trash" red wire:click="disableEntreprise({{ $entreprise }})" />
                                @else
                                <x-button.circle icon="check" green wire:click="enableEntreprise({{ $entreprise }})" />
                            @endif

                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="5">Aucune entreprises</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <x-front.pagination :pagination="$entreprises" :per-page="$perPage"/>
</div>

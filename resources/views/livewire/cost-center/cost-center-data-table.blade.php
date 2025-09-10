<div>
    <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-4">
        <div class="col-span-2 relative">
            <x-input label="Recherche" placeholder="Tapez votre recherche..." icon="magnifying-glass" wire:model.live.debounce.300ms="search"/>
            <div wire:loading wire:target="search" class="absolute right-2 top-8">
                <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <div>
            <x-native-select
                label="Item par page"
                :options="['20', '50', '100', '150', '200']"
                wire:model.live="perPage"
                class="col-span-1"
            />
        </div>
    </div>
    <div wire:loading.class="opacity-50">
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
                        <x-front.badge :success="$costcenter->is_actif" :danger="!$costcenter->is_actif">
                            {{ $costcenter->is_actif ? "Oui" : "Non" }}
                        </x-front.badge>
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="flex space-x-2">
                            <x-mini-button rounded icon="pencil" info href="{{ route('admin.costcenter.edit', ['costCenter' => $costcenter->id]) }}" />
                            <x-mini-button rounded icon="trash" red wire:click="toggleStatutCostCenter({{ $costcenter }})" />
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
    </div>
    <x-front.pagination :pagination="$costcenters" :perPage="$perPage" />
</div>

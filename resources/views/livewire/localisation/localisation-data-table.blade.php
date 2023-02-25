<div>
    <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-4">
        <div class="col-span-2">
            <x-input label="Recherche" placeholder="Tapez votre recherche..." icon="search" wire:model="search"/>
        </div>
        <div>
            <x-native-select
                label="Item par page"
                :options="['20', '50', '100', '150', '200']"
                wire:model="perPage"
                class="col-span-1"
            />
        </div>
    </div>
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
                            <x-button.circle icon="trash" red wire:click="toggleStatus({{ $localisation }})" />
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

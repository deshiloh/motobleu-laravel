<div>
    <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-4">
        <div class="col-span-2 relative">
            <x-input label="Recherche" placeholder="Tapez votre recherche..." icon="magnifying-glass" wire:model.live.debounce.300ms="search"/>
            <div wire:loading wire:target="search" class="absolute right-2 top-8">
                <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
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
    <div wire:loading.class="opacity-50">
        <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('prenom')" :direction="$sortDirection">Prénom
                </x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('email')" :direction="$sortDirection">Email
                </x-datatable.th>
                <x-datatable.th>Téléphone</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('adresse')" :direction="$sortDirection">
                    Adresse
                </x-datatable.th>
                <x-datatable.th>État</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($pilotes as $pilote)
                <x-datatable.tr>
                    <x-datatable.td>{{ $pilote->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $pilote->prenom }}</x-datatable.td>
                    <x-datatable.td>{{ $pilote->email }}</x-datatable.td>
                    <x-datatable.td>{{ $pilote->telephone }}</x-datatable.td>
                    <x-datatable.td>{{ $pilote->full_adresse }}</x-datatable.td>
                    <x-datatable.td>
                        <x-front.badge :success="$pilote->is_actif" :danger="!$pilote->is_actif">
                            {{ $pilote->is_actif ? 'Actif' : 'Non actif'}}
                        </x-front.badge>
                    </x-datatable.td>
                    <x-datatable.td>
                        <x-mini-button rounded href="{{ route('admin.pilotes.edit', ['pilote' => $pilote->id]) }}" info sm icon="pencil" />
                        <x-mini-button rounded href="{{ route('admin.pilotes.recap-reservation', ['pilote' => $pilote->id]) }}" primary sm icon="view-columns" />
                        @if($pilote->is_actif)
                            <x-mini-button rounded wire:click="disablePilote({{ $pilote }})" red sm icon="trash" />
                            @else
                            <x-mini-button rounded wire:click="enablePilote({{ $pilote }})" green sm icon="check" />
                        @endif
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="6">Aucun pilote</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
        </x-datatable>
    </div>
    <x-front.pagination :pagination="$pilotes" :per-page="$perPage" />
</div>

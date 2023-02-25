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
                        <x-button.circle href="{{ route('admin.pilotes.edit', ['pilote' => $pilote->id]) }}" info sm icon="pencil" />
                        <x-button.circle href="{{ route('admin.pilotes.recap-reservation', ['pilote' => $pilote->id]) }}" primary sm icon="view-list" />
                        @if($pilote->is_actif)
                            <x-button.circle wire:click="disablePilote({{ $pilote }})" red sm icon="trash" />
                            @else
                            <x-button.circle wire:click="enablePilote({{ $pilote }})" green sm icon="check" />
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
    <x-front.pagination :pagination="$pilotes" :per-page="$perPage" />
</div>

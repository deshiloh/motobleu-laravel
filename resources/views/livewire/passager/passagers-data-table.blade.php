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
                <x-datatable.th>Secrétaire</x-datatable.th>
                <x-datatable.th>Entreprises</x-datatable.th>
                <x-datatable.th>État</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($passagers as $passager)
                <x-datatable.tr>
                    <x-datatable.td>{{ $passager->nom }}</x-datatable.td>
                    <x-datatable.td>
                        <a href="{{ route('admin.accounts.edit', ['account' => $passager->user->id]) }}" class="text-motobleu">
                            {{ $passager->user->full_name }}
                        </a>
                    </x-datatable.td>
                    <x-datatable.td>
                        {{ implode(', ', $passager->user->entreprises()->pluck('nom')->toArray()) }}
                    </x-datatable.td>
                    <x-datatable.td>
                        <x-front.badge :success="$passager->is_actif" :danger="!$passager->is_actif">
                            {{ $passager->is_actif ? "Actif" : "Non actif" }}
                        </x-front.badge>
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="flex space-x-2">
                            <x-button.circle icon="pencil" info href="{{ route('admin.passagers.edit', ['passager' => $passager->id]) }}" />
                            @if($passager->is_actif)
                                <x-button.circle icon="x" red wire:click="disablePassenger({{ $passager }})" spinner="disablePassenger"/>
                                @else
                                <x-button.circle icon="check" green wire:click="enablePassenger({{ $passager }})" spinner="enablePassenger"/>
                            @endif
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

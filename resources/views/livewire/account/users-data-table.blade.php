<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 mb-3">
        <div class="relative">
            <x-input wire:model.live.debounce.300ms="search" label="Recherche" placeholder="Tapez votre recherche..." icon="magnifying-glass" class="md:col-span-1"/>
            <div wire:loading wire:target="search" class="absolute right-2 top-8">
                <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <x-select
            label="Entreprise"
            :async-data="route('api.entreprises')"
            option-label="nom"
            option-value="id"
            placeholder="Recherche par entreprise"
            wire:model.live="selectedEntreprise"
            class="md:col-span-1"
        />
        <x-native-select
            label="Item par page"
            :options="['20', '50', '100', '150', '200']"
            wire:model.live="perPage"
        />
        <div class="flex items-end">
            <x-button secondary label="Réinitialiser" wire:click="clearFilters" class="h-10" />
        </div>
    </div>

    <div wire:loading.class="opacity-50">
        <x-datatable>
            <x-slot name="headers">
            <tr>
                <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('prenom')" :direction="$sortDirection">
                    Prénom
                </x-datatable.th>
                <x-datatable.th>Entreprise</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('email')" :direction="$sortDirection">Email
                </x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('is_actif')">Actif</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($users as $user)
                <x-datatable.tr>
                    <x-datatable.td>{{ $user->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $user->prenom }}</x-datatable.td>
                    <x-datatable.td>
                        {{ implode(', ', $user->entreprises()->pluck('nom')->toArray()) }}
                    </x-datatable.td>
                    <x-datatable.td>{{ $user->email }}</x-datatable.td>
                    <x-datatable.td>
                        <x-front.badge :success="$user->is_actif" :danger="!$user->is_actif">
                            {{ $user->is_actif ? "Oui" : "Non" }}
                        </x-front.badge>
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="flex space-x-2">
                            <x-mini-button rounded icon="pencil" primary href="{{ route('admin.accounts.edit', ['account' => $user->id]) }}" />
                            <x-mini-button rounded icon="key" info href="{{ route('admin.accounts.password.edit', ['account' => $user->id]) }}" />
                            <x-mini-button rounded icon="building-office" emerald href="{{ route('admin.accounts.entreprise.edit', ['account' => $user->id]) }}"/>
                            @if($user->is_actif)
                                <x-mini-button rounded icon="trash" red wire:click="disableAccount({{ $user }})" />
                                @else
                                <x-mini-button rounded icon="check" green wire:click="enableAccount({{ $user }})" />
                            @endif

                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <x-datatable.tr>
                    <x-datatable.td class="text-center" colspan="5">Aucun utilisateurs</x-datatable.td>
                </x-datatable.tr>
            @endforelse
        </x-slot>
        </x-datatable>
    </div>
    <x-front.pagination :pagination="$users" :perPage="$perPage" />
</div>

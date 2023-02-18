<div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 mb-3">
        <x-input wire:model="search" label="Recherche" placeholder="Tapez votre recherche..." icon="search" class="md:col-span-1"/>
        <x-select
            label="Entreprise"
            :async-data="route('api.entreprises')"
            option-label="nom"
            option-value="id"
            placeholder="Recherche par entreprise"
            wire:model="selectedEntreprise"
            class="md:col-span-1"
        />
    </div>

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
                            <x-button.circle icon="pencil" primary href="{{ route('admin.accounts.edit', ['account' => $user->id]) }}" />
                            <x-button.circle icon="key" info href="{{ route('admin.accounts.password.edit', ['account' => $user->id]) }}" />
                            <x-button.circle icon="office-building" emerald href="{{ route('admin.accounts.entreprise.edit', ['account' => $user->id]) }}"/>
                            @if($user->is_actif)
                                <x-button.circle icon="trash" red wire:click="disableAccount({{ $user }})" />
                                @else
                                <x-button.circle icon="check" green wire:click="enableAccount({{ $user }})" />
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
    <x-front.pagination :pagination="$users" :per-page="$perPage" />
</div>

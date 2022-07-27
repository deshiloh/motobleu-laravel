<div>
    <x-datatable.search wire:model="search"/>
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th sortable wire:click="sortBy('nom')" :direction="$sortDirection">Nom</x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('prenom')" :direction="$sortDirection">Prénom
                </x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('email')" :direction="$sortDirection">Email
                </x-datatable.th>
                <x-datatable.th sortable wire:click="sortBy('entreprise')" :direction="$sortDirection">Entreprise
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
                    <x-datatable.td>{{ $user->email }}</x-datatable.td>
                    <x-datatable.td>{{ $user->entreprise->nom }}</x-datatable.td>
                    <x-datatable.td>
                        @if($user->is_actif)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"> Oui </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800"> Non </span>
                        @endif
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="flex space-x-2">
                            <x-button.circle icon="pencil" primary href="{{ route('admin.accounts.edit', ['account' => $user->id]) }}" />
                            <x-button.circle icon="key" info href="{{ route('admin.accounts.password.edit', ['account' => $user->id]) }}" />
                            <x-button.circle icon="trash" red route="{{ route('admin.accounts.destroy', ['account' => $user->id]) }}" />
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
    <div class="mt-4 px-3">
        {{ $users->links('components.datatable.pagination') }}
    </div>
</div>

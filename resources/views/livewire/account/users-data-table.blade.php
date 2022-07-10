<div>
    <x-admin.content>
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
                            <x-badge :success="$user->is_actif"></x-badge>
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="flex space-x-2">
                                <x-actions.edit href="{{ route('admin.accounts.edit', ['account' => $user->id]) }}"/>
                                <x-actions.key
                                    href="{{ route('admin.accounts.password.edit', ['account' => $user->id]) }}"/>
                                <x-actions.trash
                                    route="{{ route('admin.accounts.destroy', ['account' => $user->id]) }}"/>
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td class="text-center" colspan="5">Aucun utilisateurs</x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot>
            <x-slot:footer>
                <x-datatable.tr>
                    <x-datatable.th colspan="6">
                        <div class="px-1">
                            {{ $users->links('components.datatable.pagination') }}
                        </div>
                    </x-datatable.th>
                </x-datatable.tr>
            </x-slot:footer>
        </x-datatable>
    </x-admin.content>
</div>

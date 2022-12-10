<div>
    <div class="pb-3 border-b border-gray-200 dark:border-gray-600 sm:flex sm:items-center sm:justify-between mb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Comptes rattachés à l'entreprise</h3>
        <p class="text-white text-sm">Cliquez sur le bouton rouge pour détacher un utilisateur de l'entreprise.</p>
    </div>
    <div class="pb-3">
        <div class="grid xs:grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-select
                    wire:key="user"
                    label="Comptes"
                    placeholder="Rechercher un compte"
                    :async-data="route('admin.api.user_in_entreprise')"
                    option-label="full_name"
                    option-value="id"
                    wire:model="userId"
                />
            </div>
            <div class="pt-6">
                <x-button label="Rattacher à l'entreprise" primary wire:click="attach"/>
            </div>
        </div>
    </div>
    <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th>Nom</x-datatable.th>
                <x-datatable.th>Prénom</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @foreach($users as $user)
                <x-datatable.tr>
                    <x-datatable.td>{{ $user->nom }}</x-datatable.td>
                    <x-datatable.td>{{ $user->prenom }}</x-datatable.td>
                    <x-datatable.td>
                        <div class="flex space-x-2">
                            <x-button.circle icon="pencil" primary href="{{ route('admin.accounts.edit', ['account' => $user->id]) }}" />
                            <x-button.circle icon="trash" red route="#" wire:click="detach({{ $user->id }})"/>
                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @endforeach
        </x-slot>
        <x-slot name="tfoot">
            <tr>
                <x-datatable.td colspan="5">
                    Pagination
                </x-datatable.td>
            </tr>
        </x-slot>
    </x-datatable>
</div>

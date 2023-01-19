<div class="space-y-5">
    <x-select
        label="Rôle"
        placeholder="Sélectionnez un rôle"
        :options="$roles"
        wire:model="selectedRole"
        option-label="name"
        option-value="id"
    />

    @if($selectedRole !== null)
        <x-datatable>
            <x-slot:headers>
                <x-datatable.th>Permissions</x-datatable.th>
                <x-datatable.th>Voir</x-datatable.th>
                <x-datatable.th>Créer</x-datatable.th>
                <x-datatable.th>Éditer</x-datatable.th>
                <x-datatable.th>Supprimer</x-datatable.th>
            </x-slot:headers>
            <x-slot:body>

                @foreach($rolePermissions as $cat => $permissionsArray)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $cat }}</x-datatable.td>

                        @foreach($permissionsArray as $permission)
                            <x-datatable.td>
                                <x-checkbox id="md" md wire:model.defer="permissionsForm" value="{{ $permission['id'] }}"/>
                            </x-datatable.td>
                        @endforeach

                    </x-datatable.tr>
                @endforeach

            </x-slot:body>
        </x-datatable>

        <x-button primary label="Enregistrer" wire:click="changePermission" wire:loading.attr="disabled"/>
    @endif

</div>

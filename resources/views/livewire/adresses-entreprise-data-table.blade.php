<div>
    <x-admin.content>
        <x-datatable>
            <x-slot name="headers">
                <tr>
                    <x-datatable.th>Type</x-datatable.th>
                    <x-datatable.th>Nom</x-datatable.th>
                    <x-datatable.th>Adresse</x-datatable.th>
                    <x-datatable.th>Email contact</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse($adresses as $adresse)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $adresse->type->name }}</x-datatable.td>
                        <x-datatable.td>{{ $adresse->nom }}</x-datatable.td>
                        <x-datatable.td>{{ $adresse->adresse_full }}</x-datatable.td>
                        <x-datatable.td>{{ $adresse->email }}</x-datatable.td>
                        <x-datatable.td>
                            <div class="flex space-x-2">
                                <x-actions.edit
                                    href="{{ route('admin.entreprises.adresses.edit', ['adress' => $adresse->id, 'entreprise' => $entreprise]) }}"
                                />
                                <x-actions.trash
                                    route="{{ route('admin.entreprises.adresses.destroy', ['adress' => $adresse->id, 'entreprise' => $entreprise]) }}"
                                />
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <tr>
                        <x-datatable.td class="text-center bg-gray-700" colspan="5">
                            Aucune entreprises
                        </x-datatable.td>
                    </tr>
                @endforelse
            </x-slot>
            <x-slot name="tfoot">
                <tr>
                    <x-datatable.td colspan="5">
                        {{ $adresses->links('components.datatable.pagination') }}
                    </x-datatable.td>
                </tr>
            </x-slot>
        </x-datatable>
    </x-admin.content>
</div>

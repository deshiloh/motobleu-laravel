<div>
    <div class="pb-3 border-b border-gray-200 dark:border-gray-600 sm:flex sm:items-center sm:justify-between mb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Liste des adresses</h3>
        <div class="rounded-md bg-blue-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Heroicon name: mini/information-circle -->
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3 flex-1 md:flex md:justify-between">
                    <p class="text-sm text-blue-700">Une adresse de facturation est obligatoire, si une adresse de facturation uniquement est renseignée, elle sera utilisée pour l'adresse physique dans la partie facturation.</p>
                </div>
            </div>
        </div>

    </div>
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
                            <x-button.circle icon="pencil" primary href="{{ route('admin.entreprises.adresses.edit', ['adress' => $adresse->id, 'entreprise' => $entreprise]) }}" />
                            <x-button.circle icon="trash" red route="{{ route('admin.entreprises.adresses.destroy', ['adress' => $adresse->id, 'entreprise' => $entreprise]) }}" />
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
    </x-datatable>
</div>

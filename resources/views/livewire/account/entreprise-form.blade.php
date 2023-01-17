<div>
    <x-header>
        Entreprises lié à l'utilisateur : <span class="text-indigo-500">{{ $user->full_name }}</span>
    </x-header>
    <x-bloc-content>
        <div class="space-y-3">
            <x-errors />
            <x-select
                label="Entreprises"
                wire:model="entreprises"
                placeholder="Sélectionnez une ou plusieurs entreprises"
                multiselect
                :async-data="route('api.entreprises', ['exclude' => $exclude])"
                option-label="nom"
                option-value="id"
            />
            <x-button label="Entregistrer" primary wire:click="save" :disabled="empty($entreprises)" />
        </div>
        <hr class="my-5">
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>Nom</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($user->entreprises()->get() as $entreprise)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $entreprise->nom }}</x-datatable.td>
                        <x-datatable.td>
                            <div class="space-x-2">
                                <x-button.circle primary icon="eye" />
                                <x-button.circle red icon="x" wire:click="detach({{ $entreprise }})"/>
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                    @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="3">
                            <div class="text-center">
                                Aucune entreprise
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </x-bloc-content>
</div>

<div>
    <x-title-section>
        <x-slot:title>
            Liste des factures
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <div class="my-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <x-input label="Rechercher" icon="search" wire:model="search"/>
            </div>
            <div>
                <x-select
                    label="Entreprise"
                    wire:model="entreprise"
                    placeholder="Rechercher une entreprise"
                    :async-data="route('api.entreprises')"
                    option-label="nom"
                    option-value="id"
                />
            </div>
            <div>

            </div>
        </div>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>Référence</x-datatable.th>
                    <x-datatable.th>Date</x-datatable.th>
                    <x-datatable.th>Acquittée</x-datatable.th>
                    <x-datatable.th>Entreprise</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                <x-datatable.tr>
                    <x-datatable.td>FA2022-10-04</x-datatable.td>
                    <x-datatable.td>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</x-datatable.td>
                    <x-datatable.td>
                        <x-badges.success>
                            Oui
                        </x-badges.success>
                    </x-datatable.td>
                    <x-datatable.td>My Entreprise</x-datatable.td>
                    <x-datatable.td>
                        <x-dropdown>
                            <x-slot name="trigger">
                                <x-button label="Actions" info sm />
                            </x-slot>

                            <x-dropdown.item label="Voir" />
                            <x-dropdown.item separator label="Liste des courses" />
                        </x-dropdown>
                    </x-datatable.td>
                </x-datatable.tr>
            </x-slot:body>
        </x-datatable>
    </x-admin.content>
</div>

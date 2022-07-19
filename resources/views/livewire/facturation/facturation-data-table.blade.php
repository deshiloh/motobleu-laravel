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
                @foreach($facturations as $facture)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $facture->reference }}</x-datatable.td>
                        <x-datatable.td>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</x-datatable.td>
                        <x-datatable.td>
                            @if($facture->is_acquitte)
                                <span class="badge badge-success">
                                    Oui
                                </span>
                                @else
                                <span class="badge badge-error">
                                    Non
                                </span>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>My Entreprise</x-datatable.td>
                        <x-datatable.td>
                            <div class="dropdown">
                                <label tabindex="0" class="btn m-1 btn-primary btn-sm">Actions</label>
                                <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a href="{{ route('admin.facturations.show', ['facture' => $facture->id]) }}">Voir</a></li>
                                    <li><a href="#">Liste des courses</a></li>
                                </ul>
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforeach
            </x-slot:body>
        </x-datatable>
    </x-admin.content>
</div>

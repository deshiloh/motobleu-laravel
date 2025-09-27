@props(['entreprises'])

<div class="text-2xl mb-4 text-black dark:text-gray-200">
    Liste des entreprises à facturer
</div>

<x-datatable>
    <x-slot:headers>
        <tr>
            <x-datatable.th>Entreprise</x-datatable.th>
            <x-datatable.th>Réservations</x-datatable.th>
            <x-datatable.th>Actions</x-datatable.th>
        </tr>
    </x-slot:headers>
    <x-slot:body>
        @forelse($entreprises as $entreprise)
            <x-datatable.tr>
                <x-datatable.td>{{ $entreprise->nom }}</x-datatable.td>
                <x-datatable.td>{{ $entreprise->reservations_count }}</x-datatable.td>
                <x-datatable.td>
                    <x-billing.entreprise-action :entreprise="$entreprise" />
                </x-datatable.td>
            </x-datatable.tr>
        @empty
            <x-datatable.tr>
                <x-datatable.td colspan="3" class="text-center">
                    Aucune entreprise
                </x-datatable.td>
            </x-datatable.tr>
        @endforelse
    </x-slot:body>
</x-datatable>
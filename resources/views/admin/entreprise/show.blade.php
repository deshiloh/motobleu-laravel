<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Fiche de l'entreprise <span class="text-blue-700">{{ $entreprise->nom }}</span>
        </x-slot>
        <div class="flex space-x-2">
            @if($entreprise->adresseEntreprises()->count() < 2)
                <a class="btn btn-primary btn-sm" href="{{ route('admin.entreprises.adresses.create', ['entreprise' => $entreprise]) }}">
                    Ajouter une entreprise
                </a>
            @endif
                <a class="btn btn-primary btn-sm" href="{{ route('admin.entreprises.edit', ['entreprise' => $entreprise]) }}">
                    Modifier l'entreprise
                </a>
        </div>
    </x-title-section>
    <x-admin.content>
        <livewire:entreprise.adresses-entreprise-data-table :entreprise="$entreprise"/>
    </x-admin.content>
    <x-admin.content>
        <livewire:entreprise.recap-reservation-entreprise :entreprise="$entreprise" />
    </x-admin.content>
</x-admin-layout>

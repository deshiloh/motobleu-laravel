<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Fiche de l'entreprise <span class="text-blue-700">{{ $entreprise->nom }}</span>
        </x-slot>
        <div class="flex space-x-2">
            @if($entreprise->adresseEntreprises()->count() < 2)
                <x-link-button link="{{ route('admin.entreprises.adresses.create', ['entreprise' => $entreprise]) }}">
                    Ajouter une adresse
                </x-link-button>
            @endif
            <x-link-button link="{{ route('admin.entreprises.edit', ['entreprise' => $entreprise]) }}">
                Modifier l'entreprise
            </x-link-button>
        </div>
    </x-title-section>
    <x-admin.content>
        <livewire:adresses-entreprise-data-table :entreprise="$entreprise"/>
    </x-admin.content>
</x-admin-layout>

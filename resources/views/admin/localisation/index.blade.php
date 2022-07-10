<x-admin-layout>
    <x-title-section>
        <x-slot:title>Liste des localisations</x-slot:title>
        <a href="{{ route('admin.localisations.create') }}" class="btn btn-primary btn-sm">
            Créer une localisation
        </a>
    </x-title-section>
    <livewire:localisation.localisation-data-table />
</x-admin-layout>

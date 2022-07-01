<x-admin-layout>
    <x-title-section>
        <x-slot:title>Liste des localisations</x-slot:title>
        <x-link-button href="{{ route('admin.localisations.create') }}">Créer une localisation</x-link-button>
    </x-title-section>
    <livewire:localisation.localisation-data-table />
</x-admin-layout>

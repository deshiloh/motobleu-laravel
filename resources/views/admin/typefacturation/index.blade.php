<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            Liste des type de facturation
        </x-slot:title>
        <x-link-button href="{{ route('admin.typefacturation.create') }}">Créer un type de facturation</x-link-button>
    </x-title-section>
    <livewire:type-facturation-data-table />
</x-admin-layout>

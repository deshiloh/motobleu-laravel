<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Liste des entreprises
        </x-slot>
        <x-link-button link="{{ route('admin.entreprises.create') }}">
            Créer une entreprise
        </x-link-button>
    </x-title-section>
    <livewire:entreprises-data-table />
</x-admin-layout>

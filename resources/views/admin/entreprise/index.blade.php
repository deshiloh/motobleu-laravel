<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Liste des entreprises
        </x-slot>
        <a class="btn btn-primary btn-sm" href="{{ route('admin.entreprises.create') }}">
            Créer une entreprise
        </a>
    </x-title-section>
    <livewire:entreprise.entreprises-data-table />
</x-admin-layout>

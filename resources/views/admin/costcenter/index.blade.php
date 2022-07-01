<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            Liste des Cost Center
        </x-slot:title>
        <x-link-button href="{{ route('admin.costcenter.create') }}">Créer un Cost Center</x-link-button>
    </x-title-section>
    <livewire:cost-center.cost-center-data-table />
</x-admin-layout>

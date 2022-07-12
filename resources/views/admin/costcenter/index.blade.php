<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            Liste des Cost Center
        </x-slot:title>
        <a href="{{ route('admin.costcenter.create') }}" class="btn btn-primary btn-sm">
            Créer un Cost Center
        </a>
    </x-title-section>
    <livewire:cost-center.cost-center-data-table />
</x-admin-layout>

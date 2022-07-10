<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            Liste des type de facturation
        </x-slot:title>
        <a href="{{ route('admin.typefacturation.create') }}" class="btn btn-primary btn-sm">
            Créer un type de facturation
        </a>
    </x-title-section>
    <livewire:type-facturation.type-facturation-data-table />
</x-admin-layout>

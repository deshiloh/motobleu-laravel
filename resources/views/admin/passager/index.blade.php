<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Liste des passagers
        </x-slot>
        <a href="{{ route('admin.passagers.create') }}" class="btn btn-primary btn-sm">
            Créer un passager
        </a>
    </x-title-section>
    <livewire:passager.passagers-data-table />
</x-admin-layout>

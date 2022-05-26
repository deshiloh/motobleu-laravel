<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Liste des passagers
        </x-slot>
        <x-link-button href="{{ route('admin.passagers.create') }}">
            Créer un passager
        </x-link-button>
    </x-title-section>
    <livewire:passagers-data-table />
</x-admin-layout>

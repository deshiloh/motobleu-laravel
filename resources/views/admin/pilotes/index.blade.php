<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Liste des pilotes
        </x-slot>
        <x-link-button href="{{ route('admin.pilotes.create') }}">Créer un pilote</x-link-button>
    </x-title-section>
    <livewire:pilote-data-table />
</x-admin-layout>

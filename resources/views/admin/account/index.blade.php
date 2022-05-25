<x-admin-layout>
    <x-title-section>
        <x-slot name="title">Liste des utilisateurs</x-slot>
        <x-link-button link="{{ route('admin.accounts.create') }}">
            Créer un compte
        </x-link-button>
    </x-title-section>
    <livewire:users-data-table/>
</x-admin-layout>

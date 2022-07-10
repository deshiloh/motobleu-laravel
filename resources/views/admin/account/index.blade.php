<x-admin-layout>
    <x-title-section>
        <x-slot name="title">Liste des utilisateurs</x-slot>
        <a class="btn btn-primary btn-sm" href="{{ route('admin.accounts.create') }}">
            Créer un compte
        </a>
    </x-title-section>
    <livewire:account.users-data-table />
</x-admin-layout>

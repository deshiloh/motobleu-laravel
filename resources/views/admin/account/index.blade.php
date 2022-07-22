<x-layout>
    <x-header>
        Liste des utilisateurs
        <x-slot:right>
            <x-button label="Créer un compte" sm primary href="{{ route('admin.accounts.create') }}" />
        </x-slot:right>
    </x-header>
    <x-bloc-content>
        <livewire:account.users-data-table />
    </x-bloc-content>
</x-layout>

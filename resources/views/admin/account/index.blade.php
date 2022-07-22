<x-layout>
    @section('header')
        <x-header>
            Liste des utilisateurs
            <x-slot:right>
                <x-button label="Créer un compte" sm primary href="{{ route('admin.accounts.create') }}" />
            </x-slot:right>
        </x-header>
    @endsection
    <livewire:account.users-data-table />
</x-layout>

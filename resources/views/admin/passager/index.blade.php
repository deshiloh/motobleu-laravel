<x-layout>
    <x-header>
        Liste des passagers
        <x-slot:right>
            <x-button href="{{ route('admin.passagers.create') }}" primary sm label="Créer un passager" />
        </x-slot:right>
    </x-header>
    <x-bloc-content>
        <livewire:passager.passagers-data-table />
    </x-bloc-content>
</x-layout>

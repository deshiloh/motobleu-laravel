<x-layout>
    <x-header>
        Liste des pilotes
        <x-slot:right>
            <x-button href="{{ route('admin.pilotes.create') }}" primary sm label="Créer un pilote" />
        </x-slot:right>
    </x-header>
    <x-bloc-content>
        <livewire:pilote.pilote-data-table />
    </x-bloc-content>
</x-layout>

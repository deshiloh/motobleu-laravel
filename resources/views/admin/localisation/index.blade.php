<x-layout>
    <x-header>
        Liste des localisations
        <x-slot:right>
            <x-button href="{{ route('admin.localisations.create') }}" primary sm label="Créer une localisation" />
        </x-slot:right>
    </x-header>
    <x-bloc-content>
        <livewire:localisation.localisation-data-table />
    </x-bloc-content>
</x-layout>

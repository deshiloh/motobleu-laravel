<x-layout>
    <x-header>
        Liste des entreprises
        <x-slot:right>
            <x-button href="{{ route('admin.entreprises.create') }}" label="Créer une entreprise" sm primary/>
        </x-slot:right>
    </x-header>
    <x-bloc-content>
        <livewire:entreprise.entreprises-data-table />
    </x-bloc-content>
</x-layout>

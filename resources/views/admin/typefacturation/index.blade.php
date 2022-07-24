<x-layout>
    <x-header>
        Liste des type de facturation
        <x-slot:right>
            <x-button href="{{ route('admin.typefacturation.create') }}" primary sm label="Créer un type de facturation" />
        </x-slot:right>
    </x-header>

    <x-bloc-content>
        <livewire:type-facturation.type-facturation-data-table />
    </x-bloc-content>
</x-layout>

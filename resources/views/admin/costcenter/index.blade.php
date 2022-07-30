<x-layout>
    <x-header>
        Liste des Cost Center
        <x-slot:right>
            <x-button href="{{ route('admin.costcenter.create') }}" primary sm label="Créer un Cost Center"/>
        </x-slot:right>
    </x-header>

    <x-bloc-content>
        <livewire:cost-center.cost-center-data-table />
    </x-bloc-content>
</x-layout>

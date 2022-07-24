<x-layout>
    <x-header>
        Liste des adresses des réservations
        <x-slot:right>
            <x-button href="{{ route('admin.adresse-reservation.create') }}" primary sm label="Ajouter une adresse" />
        </x-slot:right>
    </x-header>
    <x-bloc-content>
        <livewire:reservation.adresses-reservation-data-table />
    </x-bloc-content>
</x-layout>

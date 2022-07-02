<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            Liste des adresses des réservations
        </x-slot:title>
        <x-link-button href="{{ route('admin.adresse-reservation.create') }}">Ajouter une adresse</x-link-button>
    </x-title-section>
    <livewire:reservation.adresses-reservation-data-table />
</x-admin-layout>

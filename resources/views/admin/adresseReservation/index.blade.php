<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            Liste des adresses des réservations
        </x-slot:title>
        <a href="{{ route('admin.adresse-reservation.create') }}" class="btn btn-primary btn-sm">
            Ajouter une adresse
        </a>
    </x-title-section>
    <livewire:reservation.adresses-reservation-data-table />
</x-admin-layout>

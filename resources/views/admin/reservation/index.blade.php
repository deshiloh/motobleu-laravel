<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            Liste des réservations
        </x-slot:title>
        <x-link-button href="{{ route('admin.reservations.create') }}">
            Créer une réservation
        </x-link-button>
    </x-title-section>
    <x-admin.content>
        <livewire:reservation.reservation-data-table />
    </x-admin.content>
</x-admin-layout>

<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            Liste des réservations
        </x-slot:title>
        <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary btn-sm">
            Créer une réservation
        </a>
    </x-title-section>
    <x-admin.content>
        <livewire:reservation.reservation-data-table />
    </x-admin.content>
</x-admin-layout>

<x-layout>
    <x-header>
        Liste des réservations
        <x-slot:right>
            <x-button href="{{ route('admin.reservations.create') }}" primary sm label="Créer une réservation" />
        </x-slot:right>
    </x-header>

    <x-bloc-content>
        <livewire:reservation.reservation-data-table />
    </x-bloc-content>
</x-layout>

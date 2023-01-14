<x-mail::message>
# Demande d'annulation de la réservation {{ $reservation->reference }}

L'utilisation **{{ $user->full_name }}** a demandé l'annulation de la réservation **{{ $reservation->reference }}**

<x-mail::button :url="route('admin.reservations.show', ['reservation' => $reservation->id])">
Voir la réservation
</x-mail::button>

</x-mail::message>

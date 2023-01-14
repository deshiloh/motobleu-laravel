<x-mail::message>
# Demande de modification de la réservation : {{ $reservation->reference }}
Message : <br>
{{ $message }}

<x-mail::button :url="route('admin.reservations.show', ['reservation' => $reservation->id])">
Voir la réservation
</x-mail::button>

</x-mail::message>

<x-mail::message>
# Vous avez été ajouté dans une réservation

Bonjour, <br><br>
Vous avez été ajouté en tant que pilote pour la réservation N° {{ $reservation->reference }} :<br>
<strong>Société :</strong> {{ $reservation->entreprise->nom }} <br>
<strong>Passager :</strong> {{ $reservation->passager->nom }} <br>
<strong>Lieu de départ :</strong> {{ $reservation->display_from }} <br>
<strong>Date de départ :</strong> {{ $reservation->pickup_date->format('d/m/Y') }} <br>
<strong>Heure de départ :</strong> {{ $reservation->pickup_date->format('H:i') }} <br>
<strong>Lieu de destination :</strong> {{ $reservation->display_to }} <br>

</x-mail::message>

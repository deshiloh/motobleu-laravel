<x-mail::message>
# Vous avez été retiré de la course

Bonjour,
Vous avez été retiré en tant que pilote pour la réservation N° {{ $reservation->reference }} : <br><br>
<strong>Société :</strong> {{ $reservation->entreprise->nom }} <br>
<strong>Passager :</strong> {{ $reservation->passager->nom }} <br>
<strong>Lieu de départ :</strong> {{ $reservation->display_from }} <br>
<strong>Date de départ :</strong> {{ $reservation->pickup_date->format('d/m/Y') }} <br>
<strong>Heure de départ :</strong> {{ $reservation->pickup_date->format('H:i') }} <br>
<strong>Lieu de destination :</strong> {{ $reservation->display_to }} <br>
<strong>Destinations intermédiaires :</strong> <br>
{!! nl2br($reservation->steps) !!} <br>

</x-mail::message>

@component('mail::message')
# Réservation {{ $reservation->reference }} annulée

Bonjour, <br><br>
Vous avez été retiré en tant que pilote pour la réservation N° <strong>{{ $reservation->reference }}</strong> car celle ci vient d'être annulée : <br> <br>
<strong>Société :</strong> {{ $reservation->entreprise->nom }} <br>
<strong>Passager :</strong> {{ $reservation->passager->nom }} <br>
<strong>Lieu de départ :</strong> {{ $reservation->display_from }} <br>
<strong>Date de départ :</strong> {{ $reservation->pickup_date->format('d/m/Y') }} <br>
<strong>Heure de départ :</strong> {{ $reservation->pickup_date->format('H:i') }} <br>
<strong>Lieu de destination :</strong> {{ $reservation->display_to }} <br><br><br>

@endcomponent

@component('mail::message')
Bonjour,
Votre réservation N° <strong>{{ $reservation->reference }}</strong> vient d'être confirmée !

<strong><u>Récapitulatif :</u></strong> <br>
* <strong>Passager :</strong>
  * <strong>Nom et prénom :</strong> {{ $reservation->passager->nom }}
  * <strong>Email :</strong> {{ $reservation->passager->email }}
  * <strong>Téléphone portable :</strong> {{ $reservation->passager->portable }}
  * <strong>Téléphone bureau :</strong> {{ $reservation->passager->telephone }}
<br> <br>
* <strong>Prise en charge :</strong>
  * <strong>Date :</strong> {{ $reservation->pickup_date->format('d/m/Y') }}
  * <strong>Heure :</strong> {{ $reservation->pickup_date->format('H:i') }}
  * <strong>Lieu de prise en charge :</strong> {{ $reservation->display_from }}
@if($reservation->localisation_from)
  * <strong>Provenance / N° :</strong> {{ $reservation->pickup_origin }}
@endif
<br><br>
* **Destinations intermédiaires :** <br>
{!! nl2br($reservation->steps) !!}
<br><br>
* <strong>Destination :</strong> <br>
  * <strong>Lieu de destination :</strong> {{ $reservation->display_to }}
@if($reservation->localisation_to)
  * <strong>Destination / N° :</strong> {{ $reservation->drop_off_origin }}
@endif

<strong>Commentaire :</strong>

{{ $reservation->comment }}

@endcomponent

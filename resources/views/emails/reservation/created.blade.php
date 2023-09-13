@component('mail::message')

@if($reservation->reservationBack()->exists())
Bonjour, <br>
Vos réservations N° <strong>{{ $reservation->reference }}</strong> et N° <strong>{{ $reservation->reservationBack->reference }}</strong> sont en cours de validation.<br>
Vous allez recevoir prochainement un email de confirmation.
    @else
Bonjour, <br>
Votre réservation N° <strong>{{ $reservation->reference }}</strong> est en cours de validation.<br>
Vous allez recevoir prochainement un email de confirmation.
@endif

<strong><u>Récapitulatif :</u></strong>
* <strong>Passager :</strong>
  * <strong>Nom et prénom :</strong> {{ $reservation->passager->nom }}
  * <strong>Email :</strong> {{ $reservation->passager->email }}
  * <strong>Téléphone portable : </strong> {{ $reservation->passager->portable }}
  * <strong>Téléphone bureau :</strong> {{ $reservation->passager->telephone }}

<div style="margin-top: 20px;">

* <strong>Prise en charge :</strong>
    * <strong>Date :</strong> {{ $reservation->pickup_date->format('d/m/Y') }}
    * <strong>Heure :</strong> {{ $reservation->pickup_date->format('H:i') }}
    * <strong>Lieu de prise en charge :</strong> {{ $reservation->display_from }}
@if($reservation->localisationFrom()->exists())
    * <strong>Provenance / N° : </strong> {{ $reservation->pickup_origin }}
@endif

</div>

* **Destinations intermédiaires :** <br>
    {!! nl2br($reservation->steps) !!}

<div style="margin-top: 20px;">

* <strong>Destination : </strong>
    * <strong>Lieu de destination :</strong> {{ $reservation->display_to }}
@if($reservation->localisationTo()->exists())
    * <strong>Destination / N° :</strong> {{ $reservation->drop_off_origin }}
@endif

</div>

<strong>Commentaire :</strong>

{{ $reservation->comment }}

@endcomponent

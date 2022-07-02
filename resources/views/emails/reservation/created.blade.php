@component('mail::message')

@if($reservation->reservationBack()->exists())
Bonjour,
Vos réservations N° <strong>{{ $reservation->reference }}</strong> et N° <strong>{{ $reservation->reservationBack->reference }}</strong> sont en cours de validation.<br>
Vous allez recevoir prochainement un email de confirmation.
    @else
Bonjour,
Votre réservation N° <strong>{{ $reservation->reference }}</strong> est en cours de validation.<br>
Vous allez recevoir prochainement un email de confirmation.
@endif

@if($reservation->reservationBack()->exists())
<strong><u>Récapitulatif :</u></strong>

* <strong>Passager :</strong>
    * <strong>Nom et prénom :</strong> {{ $reservation->passager->nom }}
    * <strong>Email :</strong> {{ $reservation->passager->email }}
    * <strong>Téléphone portable :</strong> {{ $reservation->passager->portable }}
    * <strong>Téléphone bureau :</strong> {{ $reservation->passager->telephone }}

## <strong>Aller (course N° {{ $reservation->reference }}) :</strong>

* <strong>Prise en charge :</strong> <br>
    * <strong>Date :</strong> {{ $reservation->pickup_date->format('d/m/Y') }}
    * <strong>Heure :</strong> {{ $reservation->pickup_date->format('H:i') }}
    * <strong>Lieu de prise en charge :</strong> {{ $reservation->display_from }}
@if($reservation->localisationFrom()->exists())
    * <strong>Provenance / N° :</strong> {{ $reservation->pickup_origin }}
@endif

<div style="margin-top: 20px">

* <strong>Destination :</strong>
    * <strong>Lieu de destination :</strong> {{ $reservation->display_to }}
@if($reservation->localisationTo()->exists())
    * <strong>Destination / N° :</strong> {{ $reservation->drop_off_origin }}
@endif

</div>

<strong>Commentaire :</strong>

{{ $reservation->comment }}

## <strong>Retour (course N° {{ $reservation->reservationBack->reference }}) :</strong>

* <strong>Prise en charge :</strong>
    * <strong>Date :</strong> {{ $reservation->pickup_date->format('d/m/Y') }}
    * <strong>Heure :</strong> {{ $reservation->pickup_date->format('H:i') }}
    * <strong>Lieu de prise en charge :</strong> {{ $reservation->display_from }}
@if($reservation->localisationFrom()->exists())
    * <strong>Provenance / N° :</strong> {{ $reservation->pickup_origin }}
@endif

<div style="margin-top: 20px;">

* <strong>Destination :</strong>
    * <strong>Lieu de destination :</strong> {{ $reservation->display_to }}
@if($reservation->localisationTo()->exists())
    * <strong>Destination / N° :</strong> {{ $reservation->drop_off_origin }}
@endif

</div>

<strong>Commentaire :</strong>

{{ $reservation->comment }}

@else

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

<div style="margin-top: 20px;">

* <strong>Destination : </strong>
    * <strong>Lieu de destination :</strong> {{ $reservation->display_to }}
@if($reservation->localisationTo()->exists())
    * <strong>Destination / N° :</strong> {{ $reservation->drop_off_origin }}
@endif

</div>

<strong>Commentaire :</strong>

{{ $reservation->comment }}

@endif

@endcomponent

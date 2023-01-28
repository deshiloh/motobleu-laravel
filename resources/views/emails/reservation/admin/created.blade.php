@component('mail::message')

@if($reservation->reservationBack()->exists())
Bonjour, <br>
Deux nouvelles réservations viennent d'être créées N° <strong>{{ $reservation->reference }}</strong> et N° <strong>{{ $reservation->reservationBack->reference }}</strong><br>
Vous pouvez les voir / modifier en cliquant sur le lien ci-dessous.
    @else
Bonjour, <br>
Une nouvelle réservation vient d'être créée N° <strong>{{ $reservation->reference }}</strong> <br>
@endif

<x-mail::button :url="route('admin.reservations.show', ['reservation' => $reservation->id])">
    Voir / Modifier la réservation
</x-mail::button>

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

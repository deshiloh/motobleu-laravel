@component('mail::message')

{!! nl2br($message) !!}

<strong><u>Récapitulatif :</u></strong>
* Passager
    * <strong>Société : {{ $reservation->entreprise->nom }}</strong>
    * <strong>Nom et prénom :</strong> {{ $reservation->passager->nom }}
    * <strong>Email :</strong> {{ $reservation->passager->email }}
    * <strong>Téléphone portable :</strong> {{ $reservation->passager->portable }}
    * <strong>Téléphone bureau :</strong> {{ $reservation->passager->telephone }}

<div style="margin-top: 20px">

* Prise en charge :
    * <strong>Date :</strong> {{ $reservation->pickup_date->format('d/m/Y') }}
    * <strong>Heure :</strong> {{ $reservation->pickup_date->format('H:i') }}
    * <strong>Lieu de prise en charge :</strong> {{ $reservation->display_from }}
    @if($reservation->pickup_origin)
    * <strong>Provenance / N° :</strong> {{ $reservation->pickup_origin }}
    @endif


</div>

@if($reservation->has_steps)
* **Destinations intermédiaires** : <br>
    {!! nl2br($reservation->steps) !!}
@endif

<div style="margin-top: 20px">

* Destination :
    * <strong>Lieu de prise en charge :</strong> {{ $reservation->display_to }}
    @if($reservation->drop_off_origin)
    * <strong>Destination / N° :</strong> {{ $reservation->drop_off_origin }}
    @endif
</div>

<strong>Commentaire</strong>

{{ $reservation->comment }}
@endcomponent

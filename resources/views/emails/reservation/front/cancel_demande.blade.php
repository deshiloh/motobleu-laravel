<x-mail::message>
Bonjour,

L'assistant(e) **{{ $user->full_name }}** a demandé l'annulation de la réservation **{{ $reservation->reference }}**

**Récapitulatif :** <br>
**Passager :**
* **Nom et prénom** : {{ $reservation->passager->nom }}
* **Email** : {{ $reservation->passager->email }}
* **Téléphone portable** : {{ $reservation->passager->portable }}
* **Téléphone bureau** : {{ $reservation->passager->telephone }}

**Prise en charge :**<br>
* **Date** : {{ $reservation->pickup_date->format('d/m/Y') }}
* **Heure** : {{ $reservation->pickup_date->format('H:i') }}
* **Lieu de prise en charge** : {{ $reservation->display_from }}
@if($reservation->pickup_origin)
* **Provenance / N°** : {{ $reservation->pickup_origin }}
@endif

@if($reservation->has_steps)
<div style="margin-top: 20px">

**Destinations intermédiaires :** <br>
    {!! nl2br($reservation->steps) !!}

</div>
@endif

**Destination :** <br>

* **Lieu de destination** : {{ $reservation->display_to }}
@if($reservation->drop_off_origin)
* **Destination / N°** : {{ $reservation->drop_off_origin }}
@endif

**Commentaire :** <br>
{{ $reservation->comment ?? 'Aucun commentaire' }}

<x-mail::button :url="route('admin.reservations.show', ['reservation' => $reservation->id])">
Voir la réservation
</x-mail::button>

</x-mail::message>

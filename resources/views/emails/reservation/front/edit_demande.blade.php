<x-mail::message>
Bonjour,<br>
L'assistant(e) **{{ $reservation->passager->user->full_name }}** a demandée de modifier la réservation **{{ $reservation->reference }}** :

> {{ $message }}

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

**Destination :** <br>

* **Lieu de destination** : {{ $reservation->display_to }}
@if($reservation->drop_off_origin)
* **Provenance / N°** : {{ $reservation->drop_off_origin }}
@endif

**Commentaire :** <br>
{{ $reservation->comment ?? 'Aucun commentaire' }}
<x-mail::button :url="route('admin.reservations.show', ['reservation' => $reservation->id])">
Voir la réservation
</x-mail::button>

</x-mail::message>

<x-mail::message>
# Modification de réservation

Bonjour, <br>
Votre réservation N° {{ $reservation->reference }} vient d'être modifiée.<br>

**Récapitulatif :** <br> <br>

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
{{ $reservation->comment }}

</x-mail::message>

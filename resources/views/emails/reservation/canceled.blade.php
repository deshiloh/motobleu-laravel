@component('mail::message')

Bonjour, <br>
Votre annulation pour la réservation **{{ $reservation->reference }}** a bien été prise en compte. <br> <br>

## Passager
- **Nom et prénom :** {{ $reservation->passager->nom }}
- **Email :** {{ $reservation->passager->email }}
- **Téléphone portable :** {{ $reservation->passager->portable ?? 'Non communiqué' }}
- **Téléphone bureau :** {{ $reservation->passager->telephone ?? 'Non communiqué' }}

## Départ :
- **Date :** {{ $reservation->pickup_date->format('d/m/Y') }}
- **Heure :** {{ $reservation->pickup_date->format('H:i') }}
- **Lieu du départ :** {{ $reservation->display_from }}
@if($reservation->pickup_origin)
- **Provenance / N° :** {{ $reservation->pickup_origin }}
@endif

@if($reservation->has_steps)
**Destinations intermédiaires :** <br>

{!! nl2br($reservation->steps) !!}
@endif


## Destination :
- **Lieu de destination :** {{ $reservation->display_to }}
@if($reservation->drop_off_origin)
- **Destination / N° :** {{ $reservation->drop_off_origin }}
@endif

**Commentaire :** <br>
{{ $reservation->comment ?? 'Aucun commentaire' }}

@endcomponent

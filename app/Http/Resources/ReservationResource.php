<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'statut' => $this->statut,
            'encompte_pilote' => $this->encompte_pilote,
            'encaisse_pilote' => $this->encaisse_pilote,
            'pickup_date' => $this->pickup_date->format('c'),
            'pickup_origin' => $this->pickup_origin,
            'locationFrom' => $this->whenLoaded('localisationFrom'),
            'locationTo' => $this->whenLoaded('localisationTo'),
            'adresse_from' => $this->whenLoaded('adresseReservationFrom'),
            'adresse_to' => $this->whenLoaded('adresseReservationTo'),
            'passager' => new PassagerResource($this->whenLoaded('passager')),
            'pilote' => $this->whenLoaded('pilote'),
            'entreprise' => $this->whenLoaded('entreprise'),
            'account' => $this->whenLoaded('passager', function() {
                return new UserResource($this->passager->user);
            })
        ];
    }
}

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
            'encompte_pilote' => $this->encompte_pilote ?? 0.0,
            'encaisse_pilote' => $this->encaisse_pilote ?? 0.0,
            'pickup_date' => $this->pickup_date->format('c'),
            'pickup_origin' => $this->pickup_origin,
            'location_from' => $this->whenLoaded('localisationFrom'),
            'location_to' => $this->whenLoaded('localisationTo'),
            'adresse_from' => $this->whenLoaded('adresseReservationFrom'),
            'adresse_to' => $this->whenLoaded('adresseReservationTo'),
            'passager' => new PassagerResource($this->whenLoaded('passager')),
            'pilote' => $this->whenLoaded('pilote'),
            'entreprise' => $this->whenLoaded('entreprise'),
            'account' => $this->whenLoaded('passager', function() {
                return new UserResource($this->passager->user);
            }),
            'send_to_passager' => $this->send_to_passager,
            'calendar_passager_invitation' => $this->calendar_passager_invitation,
            'comment_pilote' => $this->comment_pilote ?? "",
            'comment' => $this->comment ?? ""
        ];
    }
}

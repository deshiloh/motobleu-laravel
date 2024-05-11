<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdressesReservationResource extends JsonResource
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
            'adresse' => $this->adresse,
            'adresse_complement' => $this->adresse_complement,
            'code_postal' => $this->code_postal,
            'ville' => $this->ville,
        ];
    }
}

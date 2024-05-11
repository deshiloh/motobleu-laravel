<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'nom' => $this->nom,
            'adresse' => $this->adresse,
            'adresse_complement' => $this->adresse_complement,
            'code_postal' => $this->code_postal,
            'ville' => $this->ville,
            'is_actif' => $this->is_actif,
            'telephone' => $this->telephone,
        ];
    }
}

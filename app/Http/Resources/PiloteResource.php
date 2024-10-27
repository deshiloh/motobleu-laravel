<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PiloteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "prenom" => $this->prenom,
            "telephone" => $this->telephone,
            "email" => $this->email,
            "entreprise" => $this->entreprise,
            "adresse" => $this->adresse,
            "adresse_complement" => $this->adresse_complement,
            "code_postal" => $this->code_postal,
            "ville" => $this->ville,
            "is_actif" => $this->is_actif,
            "commission" => $this->commission
        ];
    }
}

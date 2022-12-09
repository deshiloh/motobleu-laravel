<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @mixin IdeHelperLocalisation
 */
class Localisation extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    /**
     * @return string
     */
    public function getFullAdresseAttribute(): string
    {
        return $this->adresse . ' '. $this->adresse_complement. ' '. $this->code_postal.' '.$this->ville;
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'nom' => $this->nom
        ];
    }
}

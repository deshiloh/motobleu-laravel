<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @mixin IdeHelperPilote
 */
class Pilote extends Model
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'entreprise',
        'telephone',
        'adresse',
        'adresse_complement',
        'code_postal',
        'ville',
    ];

    /**
     * @return Attribute
     */
    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return implode(' ', [$this->nom, $this->prenom]);
            },
            set: function ($value) {
                return $value;
            }
        );
    }

    public function toSearchableArray(): array
    {
        return [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
        ];
    }
}

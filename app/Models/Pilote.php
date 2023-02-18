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
    use HasFactory;

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
        'is_actif',
        'ville',
    ];

    protected $appends = [
        'full_name'
    ];

    protected $casts = [
        'is_actif' => 'boolean'
    ];

    /**
     * @return Attribute
     */
    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $att = [];

                if (isset($attributes['nom'])) {
                    $att[] = $attributes['nom'];
                }

                if (isset($attributes['prenom'])) {
                    $att[] = $attributes['prenom'];
                }

                return implode(' ', $att);
            }
        );
    }
}

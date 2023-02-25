<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected $casts = [
        'is_actif' => "boolean"
    ];

    public function fullAdresse(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => implode(' ',
                [
                    $attributes['nom'],
                    $attributes['adresse'],
                    $attributes['adresse_complement'],
                    $attributes['code_postal'],
                    $attributes['ville']
                ]
            )
        );
    }
}

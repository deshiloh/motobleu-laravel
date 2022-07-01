<?php

namespace App\Models;

use App\Enum\AdresseEntrepriseTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdresseEntreprise extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => AdresseEntrepriseTypeEnum::class
    ];

    /**
     * @return BelongsTo
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    /**
     * @return Attribute
     */
    public function typeName(): Attribute
    {
        return new Attribute(
            get: function($value, $attributes) {
                return $this->type->name;
            }
        );
    }

    /**
     * @return Attribute
     */
    public function adresseFull(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                $text = '';
                $text .= (isset($attributes['adresse'])) ? $attributes['adresse'] : '';
                $text .= (isset($attributes['adresse_complement'])) ? ', ' . $attributes['adresse_complement'] : '';
                $text .= (isset($attributes['code_postal'])) ? ', ' . $attributes['code_postal'] : '';
                $text .= (isset($attributes['ville'])) ? ' ' . $attributes['ville'] : '';
                return $text;
            }
        );
    }
}

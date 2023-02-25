<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

/**
 * @mixin IdeHelperAdresseReservation
 */
class AdresseReservation extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $appends = [
        'full_adresse'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retourne l'adresse complète
     * @return Attribute
     */
    public function fullAdresse(): Attribute
    {
        return Attribute::make(
            get: function ($value , $attribute) {
                $text = "";

                if (isset($attribute['adresse'])) {
                    $text .= $attribute['adresse'] . ' ';
                }
                if (isset($attribute['adresse_complement'])) {
                    $text .= $attribute['adresse_complement'] . ' ';
                }
                if (isset($attribute['code_postal'])) {
                    $text .= $attribute['code_postal'] . ' ';
                }
                if (isset($attribute['ville'])) {
                    $text .= $attribute['ville'] . ' ';
                }

                return $text;
            }
        );
    }
}

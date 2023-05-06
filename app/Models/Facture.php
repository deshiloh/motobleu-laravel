<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperFacture
 */
class Facture extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = [
        'reservations.entreprise'
    ];

    public function reference(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_null($value)) {
                    return $this->generateReference();
                }

                return $value;
            },
            set: function ($value) {
                if (is_null($value)) {
                    return $this->generateReference();
                }

                return $value;
            }
        );
    }

    public function addressBillInline(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                return str_replace('<br>', ', ', $attributes['adresse_facturation']);
            }
        );
    }

    public function addressClientInline(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                return str_replace('<br>', ', ', $attributes['adresse_client']);
            }
        );
    }

    /**
     * @return HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function montantTtc(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $this->generateMontantTtc();
            }
        );
    }

    /**
     * Génération de la référence d'une facture
     * @param string $year
     * @param string $month
     * @return string
     */
    public static function generateReference(string $year, string $month): string
    {
        return sprintf('FA%04d-%02d-%02d',
            $year,
            $month,
            Facture::where('month', $month)->where('year', $year)->count() + 1
        );
    }

    private function generateMontantTtc(): float
    {
        $ttc = $this->montant_ht + ($this->montant_ht * 0.1);
        return $ttc;
    }
}

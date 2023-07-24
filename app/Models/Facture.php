<?php

namespace App\Models;

use App\Enum\BillStatut;
use App\Enum\ReservationStatus;
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

    protected $casts = [
        'statut' => BillStatut::class,
        'is_acquitte' => 'boolean'
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
                return str_replace(['<br>', '<br />'], ', ', $attributes['adresse_facturation']);
            }
        );
    }

    public function addressClientInline(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                return str_replace(['<br>', '<br />'], ', ', $attributes['adresse_client']);
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

    public function montantHt(): Attribute
    {
        return new Attribute(
            get: function($value, $attributes) {
                return $attributes['montant_ttc'] / 1.10;
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
}

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

    private function generateReference(): string
    {
        $currentDate = Carbon::now();
        return sprintf('FA%s-%s-%s',
            $currentDate->year,
            $currentDate->month,
            Facture::where('month', $currentDate->month)->where('year', $currentDate->year)->count() + 1
        );
    }

    public function montantTtc(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return $this->generateMontantTtc();
            }
        );
    }

    private function generateMontantTtc(): float
    {
        $ttc = $this->montant_ht + ($this->montant_ht * 0.1);
        return $ttc;
    }

    /**
     * @return HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($facture) {
            $currentDate = Carbon::now();
            if (is_null($facture->reference)) {

                $reference = sprintf('FA%s-%s-%s',
                    $currentDate->year,
                    $currentDate->month,
                    Facture::where('month', $currentDate->month)->where('year', $currentDate->year)->count() + 1
                );

                $facture->reference = $reference;
            }
        });
    }
}

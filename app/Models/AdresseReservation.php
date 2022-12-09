<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class AdresseReservation extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAdresseAttribute(): string
    {
        return $this->adresse . ' ' . $this->adresse_complement . ' ' . $this->code_postal . ' ' . $this->ville;
    }

    public function toSearchableArray(): array
    {
        return [
            'adresse' => $this->adresse,
            'ville' => $this->ville
        ];
    }
}

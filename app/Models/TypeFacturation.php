<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class TypeFacturation extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'nom' => $this->nom
        ];
    }
}

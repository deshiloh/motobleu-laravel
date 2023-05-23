<?php

namespace App\Models;

use App\Enum\AdresseEntrepriseTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Scout\Searchable;
use Livewire\WithPagination;

/**
 * @mixin IdeHelperEntreprise
 */
class Entreprise extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = ['nom'];
    protected $casts = [
        'is_actif' => 'boolean'
    ];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return HasMany
     */
    public function adresseEntreprises(): HasMany
    {
        return $this->hasMany(AdresseEntreprise::class);
    }

    /**
     * @return HasMany
     */
    public function typeFacturations(): HasMany
    {
        return $this->hasMany(TypeFacturation::class);
    }

    /**
     * Récupère l'adresse de facturation de l'entreprise
     * @return Model|AdresseEntreprise
     */
    public function getBilledAddress(): Model|AdresseEntreprise
    {
        return $this->adresseEntreprises()
            ->where('type', AdresseEntrepriseTypeEnum::FACTURATION->value)
            ->first();
    }

    /**
     * @return bool
     */
    public function hasBilledAddress(): bool
    {
        return $this->adresseEntreprises()
            ->where('type', AdresseEntrepriseTypeEnum::FACTURATION->value)
            ->exists();
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}

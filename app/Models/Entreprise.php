<?php

namespace App\Models;

use App\Enum\AdresseEntrepriseTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Livewire\WithPagination;

class Entreprise extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['nom'];

    /**
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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
     * @param AdresseEntrepriseTypeEnum $adresseEntrepriseTypeEnum
     * @return Model|HasMany|null
     */
    public function getAdresse(AdresseEntrepriseTypeEnum $adresseEntrepriseTypeEnum): Model|HasMany|null
    {
        return $this->adresseEntreprises()->where('type', $adresseEntrepriseTypeEnum->value)->first();
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

/**
 * @property boolean $is_actif
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword, Searchable;

    protected $appends = ['full_name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'adresse',
        'adresse_bis',
        'code_postal',
        'ville',
        'is_admin_ardian',
        'is_actif'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return BelongsToMany
     */
    public function entreprises(): BelongsToMany
    {
        return $this->belongsToMany(Entreprise::class);
    }

    /**
     * @return HasMany
     */
    public function passagers(): HasMany
    {
        return $this->hasMany(Passager::class);
    }

    /**
     * @return HasMany
     */
    public function adresseReservations(): HasMany
    {
        return $this->hasMany(AdresseReservation::class);
    }

    /**
     * @return HasManyThrough
     */
    public function reservations(): HasManyThrough
    {
        return $this->hasManyThrough(Reservation::class, Passager::class);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return implode(' ', [$this->nom, $this->prenom]);
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->full_name,
            'email' => $this->email,
            'is_actif' => $this->is_actif
        ];
    }

    /**
     * @return array
     */
    public static function selectInputDatas(): array
    {
        $selectDatas = [];

        foreach (self::all() as $data) {
            $selectDatas[$data->id] = $data->nom;
        }

        return $selectDatas;
    }
}

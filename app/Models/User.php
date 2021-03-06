<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @return BelongsTo
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
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

    public function reservations()
    {
        return $this->hasManyThrough(Reservation::class, Passager::class);
    }

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->nom, $this->prenom]);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'nom' => $this->full_name,
            'email' => $this->email,
            'entreprise' => $this->entreprise()->first()->nom,
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

<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
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
use Spatie\Permission\Traits\HasRoles;

/**
 * @property boolean $is_actif
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword, HasRoles;

    protected $appends = ['full_name'];
    protected $with = [
        'entreprises'
    ];

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
        'is_admin',
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

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $attr = [];

                if (isset($attributes['nom'])) {
                    $attr[] = $attributes['nom'];
                }

                if (isset($attributes['prenom'])) {
                    $attr[] = $attributes['prenom'];
                }

                return implode(' ', $attr);
            }
        );
    }

    /**
     * Envoi l'email de reset mot de passe
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = route('password.reset', ['token' => $token]);
        $this->notify(new ResetPasswordNotification($url));
    }
}

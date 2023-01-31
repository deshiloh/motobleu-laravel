<?php

namespace App\Models;

use App\Enum\ReservationStatus;
use Carbon\Carbon;
use Google_Service_Calendar_Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;
use Spatie\GoogleCalendar\Event;

/**
 * @mixin IdeHelperReservation
 */
class Reservation extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $with = [
        'passager' => [
            'user'
        ],
        'localisationFrom',
        'localisationTo',
        'adresseReservationFrom',
        'adresseReservationTo',
        'entreprise'
    ];

    protected $casts = [
        'statut' => ReservationStatus::class,
        'pickup_date' => 'datetime:Y-m-d H:i:s',
        'drop_date' => 'datetime:Y-m-d H:i:s',
        'has_back' => 'boolean',
        'send_to_passager' => 'boolean',
        'send_to_user' => 'boolean',
        'tarif' => 'float',
        'majoration' => 'float',
        'complement' => 'float'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function ($reservation) {
            if (is_null($reservation->reference)) {
                $currentDate = Carbon::now();
                $reservation->reference = $currentDate->format('Ym').Reservation::count() + 1;
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function passager(): BelongsTo
    {
        return $this->belongsTo(Passager::class);
    }

    public function localisationFrom(): BelongsTo
    {
        return $this->belongsTo(Localisation::class);
    }

    public function localisationTo(): BelongsTo
    {
        return $this->belongsTo(Localisation::class);
    }

    public function adresseReservationFrom(): BelongsTo
    {
        return $this->belongsTo(AdresseReservation::class);
    }

    public function adresseReservationTo(): BelongsTo
    {
        return $this->belongsTo(AdresseReservation::class);
    }

    public function reservationBack(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function pilote(): BelongsTo
    {
        return $this->belongsTo(Pilote::class);
    }

    public function scopeToConfirmed(Builder $query)
    {
        return $query->where('statut', ReservationStatus::Created);
    }

    /**
     * @return false|Google_Service_Calendar_Event
     */
    public function getEvent()
    {
        $event = false;
        if ($this->event_id) {
            $event = Event::find($this->event_id)->googleEvent;
        }
        return $event;
    }

    public function getDisplayFromAttribute(): string
    {
        $text = '';

        if ($this->localisation_from_id) {
            $text = $this->localisationFrom->full_adresse;
        }
        if ($this->adresse_reservation_from_id) {
            $text = $this->adresseReservationFrom->full_adresse;
        }

        return $text;
    }
    public function getDisplayToAttribute(): string
    {
        $text = '';

        if ($this->localisation_to_id) {
            $text = $this->localisationTo->full_adresse;
        }
        if ($this->adresse_reservation_to_id) {
            $text = $this->adresseReservationTo->full_adresse;
        }

        return $text;
    }

    /**
     * Retourne le total TTC de la réservation
     * @return int|float
     */
    public function getTotalTtcAttribute(): int|float
    {
        $total = floatval($this->tarif);
        $montantMajoration = $total * (floatval($this->majoration) / 100);
        return $total + $montantMajoration + floatval($this->complement);
    }
}

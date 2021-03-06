<?php

namespace App\Models;

use Carbon\Carbon;
use Google_Service_Calendar_Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\GoogleCalendar\Event;

class Reservation extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    protected $casts = [
        'pickup_date' => 'datetime:Y-m-d H:i:s',
        'drop_date' => 'datetime:Y-m-d H:i:s',
        'is_confirmed' => 'boolean',
        'is_cancel' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($reservation) {
            $currentDate = Carbon::now();
            if (is_null($reservation->reference)) {
                $reservation->reference = $currentDate->format('Yd').Reservation::count() + 1;
            }
        });
    }

    public function passager()
    {
        return $this->belongsTo(Passager::class);
    }

    public function localisationFrom()
    {
        return $this->belongsTo(Localisation::class);
    }

    public function localisationTo()
    {
        return $this->belongsTo(Localisation::class);
    }

    public function adresseReservationFrom()
    {
        return $this->belongsTo(AdresseReservation::class);
    }

    public function adresseReservationTo()
    {
        return $this->belongsTo(AdresseReservation::class);
    }

    public function reservationBack()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function pilote()
    {
        return $this->belongsTo(Pilote::class);
    }

    public function scopeToConfirmed(Builder $query)
    {
        return $query->where([
            ['is_confirmed', '=', false],
            ['is_cancel', '=', false]
        ]);
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

    public function getDisplayToAttribute()
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

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'pickup_date' => $this->pickup_date,
            'user' => $this->passager->user()->first()->full_name,
            'entreprise' => $this->passager->user()->first()->entreprise->nom,
            'passager' => $this->passager()->first()->nom,
            'localisation_from' => $this->display_from,
            'localisation_to' => $this->display_to,
            'pilote' => $this->pilote() === null ? $this->pilote()->first()->full_name : null,
            'comment' => $this->comment,
            'pickup_origin' => $this->pickup_origin,
            'is_cancel' => $this->is_cancel,
            'is_confirmed' => $this->is_confirmed,
        ];
    }
}

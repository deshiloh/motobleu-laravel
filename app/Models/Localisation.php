<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Localisation extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function getFullAdresseAttribute()
    {
        return $this->adresse . ' '. $this->adresse_complement. ' '. $this->code_postal.' '.$this->ville;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Pilote extends Model
{
    use HasFactory, Searchable;

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
        'entreprise',
        'telephone',
        'adresse',
        'adresse_complement',
        'code_postal',
        'ville',
    ];

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->nom, $this->prenom]);
    }
}

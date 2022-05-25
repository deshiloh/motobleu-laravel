<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Livewire\WithPagination;

class Entreprise extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];

    public function adresseEntreprises()
    {
        return $this->hasMany(AdresseEntreprise::class);
    }
}

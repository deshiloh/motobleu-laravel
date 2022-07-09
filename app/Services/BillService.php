<?php

namespace App\Services;

use App\Models\Entreprise;
use App\Models\Facture;
use Illuminate\Database\Eloquent\Collection;

class BillService
{
    /**
     * Génère la facture et l'enregistre dans la base de donneés
     * @param Facture $facture
     * @param Entreprise $entreprise
     * @param Collection $reservations
     * @return void
     */
    public static function generateAndCreate(Facture $facture, Entreprise $entreprise, Collection $reservations)
    {
        //$nbFacture = Facture::query()
        // Création de la facture

        // Récupération de l'adresse du client et de l'adresse de sa facturation

        // Calcul du montant de toutes les réservations (TTC)

        // Calcul du total HT
        // $prixHT = $prixTTC / 1.10;

    }
}

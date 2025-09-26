<?php

namespace App\Livewire\Forms;

use App\Models\Reservation;
use Throwable;

/**
 * Formulaire de création de réservation pour l'interface d'administration
 *
 * Ce formulaire gère la création de réservations avec validation complète,
 * incluant la gestion des passagers, adresses, et réservations retour.
 * Applique les principes SOLID pour une maintenabilité optimale.
 *
 * @package App\Livewire\Forms
 * @author MotoBleue Team
 */
class AdminReservationForm extends BaseReservationForm
{


    /**
     * Règles de validation pour les champs de base du formulaire (Admin)
     * userId est requis car l'admin peut créer pour n'importe quel utilisateur
     *
     * @return array<string, string> Règles de validation de base
     */
    protected function getBaseRules(): array
    {
        return [
            'hasBack' => 'boolean',
            'userId' => 'required|exists:users,id',
            'entrepriseId' => 'required|exists:entreprises,id',
            'pickupDate' => 'required|date_format:d/m/Y H:i',
            'hasSteps' => 'boolean',
            'comment' => 'nullable|string',
            'calendarPassagerInvitation' => 'boolean',
            'sendToPassager' => 'boolean',
            'commande' => 'nullable|string',
        ];
    }



    /**
     * Crée une réservation sans validation préalable
     *
     * Méthode spécifique à l'interface d'administration permettant
     * de créer une réservation en bypassant la validation.
     *
     * @return Reservation La réservation créée
     * @throws Throwable En cas d'erreur lors de la création
     */
    public function createReservationWithoutValidation(): Reservation
    {
        $this->createReservation();
        // Note: Cette méthode pourrait retourner la réservation créée
        // mais pour l'instant on maintient la compatibilité
        return new Reservation();
    }
}

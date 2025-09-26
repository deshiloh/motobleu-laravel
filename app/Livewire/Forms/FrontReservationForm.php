<?php

namespace App\Livewire\Forms;

use Throwable;

/**
 * Formulaire de création de réservation pour l'interface client
 *
 * Ce formulaire gère la création de réservations côté client avec des règles
 * adaptées à l'usage front-end (userId optionnel car géré par l'authentification).
 *
 * @package App\Livewire\Forms
 * @author MotoBleue Team
 */
class FrontReservationForm extends BaseReservationForm
{
    // === NOTIFICATIONS SPÉCIFIQUES AU FRONT ===
    /** Envoyer une invitation calendrier au passager (nommage front-end) */
    public bool $calendarPassengerInvitation = true;

    /** Envoyer une notification au passager (nommage front-end) */
    public bool $sendToPassenger = true;

    /**
     * Règles de validation pour les champs de base du formulaire (Front)
     * Adapté pour l'interface client - userId optionnel car géré par l'authentification
     *
     * @return array<string, string> Règles de validation de base
     */
    protected function getBaseRules(): array
    {
        return [
            'hasBack' => 'boolean',
            'userId' => 'nullable|exists:users,id',
            'entrepriseId' => 'required|exists:entreprises,id',
            'pickupDate' => 'required|date_format:d/m/Y H:i',
            'hasSteps' => 'boolean',
            'comment' => 'nullable|string',
            'calendarPassengerInvitation' => 'boolean',
            'sendToPassenger' => 'boolean',
            'commande' => 'nullable|string',
        ];
    }

    /**
     * Sauvegarde la réservation avec validation
     *
     * Méthode publique simplifiée pour l'interface front-end.
     *
     * @throws Throwable En cas d'erreur lors de la création
     */
    public function save(): void
    {
        $this->validate();
        $this->createReservation();
    }
}
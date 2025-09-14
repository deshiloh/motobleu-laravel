<?php

namespace App\Services;

/**
 * Service de validation des réservations
 *
 * Centralise les règles de validation communes entre les formulaires
 * de réservation Admin et Front pour éviter la duplication de code.
 *
 * @package App\Services
 * @author MotoBleue Team
 * @version 1.0
 */
class ReservationValidationService
{
    /**
     * Règles de validation communes pour les passagers
     *
     * @return array<string, string>
     */
    public static function getCommonPassengerRules(): array
    {
        return [
            'nom' => 'required|string',
            'email' => 'required|email',
            'portable' => 'required|string',
            'telephone' => 'nullable|string',
        ];
    }

    /**
     * Règles de validation communes pour les adresses
     *
     * @return array<string, string>
     */
    public static function getCommonAddressRules(): array
    {
        return [
            'adresse' => 'required|string',
            'code_postal' => 'required|string',
            'ville' => 'required|string',
        ];
    }

    /**
     * Règles de validation pour les localisations de lieux
     *
     * @return array<string, string>
     */
    public static function getCommonLocationRules(): array
    {
        return [
            'localisation_id' => 'required|integer',
            'origin' => 'nullable|string',
        ];
    }

    /**
     * Règles de validation pour les données de réservation de base
     *
     * @return array<string, string>
     */
    public static function getCommonReservationRules(): array
    {
        return [
            'pickup_date' => 'required|date',
            'commande' => 'nullable|string',
            'comment' => 'nullable|string',
            'send_to_passager' => 'boolean',
            'calendar_passager_invitation' => 'boolean',
            'has_steps' => 'boolean',
            'steps' => 'nullable|string',
        ];
    }

    /**
     * Règles de validation pour la correction des passagers (cost center)
     *
     * @return array<string, string>
     */
    public static function getPassengerCorrectionRules(): array
    {
        return [
            'cost_center_id' => 'required',
            'type_facturation_id' => 'required',
        ];
    }

    /**
     * Vérifie si une entreprise nécessite des informations cost center
     *
     * @param int|null $entrepriseId
     * @return bool
     */
    public static function requiresCostCenter(?int $entrepriseId): bool
    {
        if (is_null($entrepriseId)) {
            return false;
        }

        $billSettings = app(\app\Settings\BillSettings::class);
        return in_array($entrepriseId, $billSettings->entreprises_cost_center_facturation);
    }

    /**
     * Obtient les règles de validation pour la correction d'un passager avec préfixe
     *
     * @param string $prefix Préfixe pour les champs (ex: 'form.passengerInError')
     * @return array<string, string>
     */
    public static function getPassengerCorrectionRulesWithPrefix(string $prefix): array
    {
        $baseRules = self::getPassengerCorrectionRules();
        $rules = [];

        foreach ($baseRules as $field => $rule) {
            $rules["{$prefix}.{$field}"] = $rule;
        }

        return $rules;
    }
}
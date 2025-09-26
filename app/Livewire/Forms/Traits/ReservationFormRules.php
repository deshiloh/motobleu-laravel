<?php

namespace App\Livewire\Forms\Traits;

use App\Services\ReservationService;
use App\Services\ReservationValidationService;
use Carbon\Carbon;

trait ReservationFormRules
{
    /**
     * Règles de validation pour la gestion des passagers
     *
     * Applique le principe ouvert/fermé (OCP) en utilisant des services
     * externes pour les règles métier spécialisées.
     *
     * @return array<string, string> Règles de validation pour les passagers
     */
    protected function getPassengerRules(): array
    {
        return match ($this->passengerMode) {
            ReservationService::EXIST_PASSAGER => $this->getExistingPassengerRules(),
            ReservationService::NEW_PASSAGER => $this->getNewPassengerRules(),
            default => []
        };
    }

    /**
     * Règles pour un passager existant
     *
     * @return array<string, string>
     */
    private function getExistingPassengerRules(): array
    {
        return ['passengerId' => 'required|integer'];
    }

    /**
     * Règles pour un nouveau passager
     *
     * @return array<string, string>
     */
    private function getNewPassengerRules(): array
    {
        $rules = $this->buildNewPassengerBaseRules();

        if ($this->requiresCostCenterValidation()) {
            $rules = array_merge($rules, $this->buildCostCenterRules());
        }

        return $rules;
    }

    /**
     * Construit les règles de base pour un nouveau passager
     *
     * @return array<string, string>
     */
    private function buildNewPassengerBaseRules(): array
    {
        $commonRules = ReservationValidationService::getCommonPassengerRules();
        $rules = [];

        foreach ($commonRules as $field => $rule) {
            $rules["newPassager.{$field}"] = $rule;
        }

        return $rules;
    }

    /**
     * Vérifie si la validation du cost center est requise
     *
     * @return bool
     */
    private function requiresCostCenterValidation(): bool
    {
        return !is_null($this->entrepriseId) &&
               ReservationValidationService::requiresCostCenter($this->entrepriseId);
    }

    /**
     * Construit les règles pour le cost center
     *
     * @return array<string, string>
     */
    private function buildCostCenterRules(): array
    {
        $costCenterRules = ReservationValidationService::getPassengerCorrectionRules();
        $rules = [];

        foreach ($costCenterRules as $field => $rule) {
            $rules["newPassager.{$field}"] = $rule;
        }

        return $rules;
    }

    /**
     * Construit les règles de validation pour une nouvelle adresse
     *
     * Méthode utilitaire qui évite la duplication de code entre
     * les adresses de départ et d'arrivée.
     *
     * @param string $prefix Préfixe pour les champs d'adresse
     * @return array<string, string>
     */
    protected function buildAddressRules(string $prefix): array
    {
        $commonRules = ReservationValidationService::getCommonAddressRules();

        return array_combine(
            array_map(fn($key) => "{$prefix}.{$key}", array_keys($commonRules)),
            array_values($commonRules)
        );
    }

    /**
     * Construit la règle de validation pour la date de retour
     *
     * La date de retour doit être postérieure à la date aller.
     *
     * @return string Règle de validation Laravel
     */
    protected function buildBackDateRule(): string
    {
        if (!$this->pickupDate) {
            return 'required|date';
        }

        $parsedDate = Carbon::createFromFormat('d/m/Y H:i', $this->pickupDate);
        return 'required|date|after:' . $parsedDate->format('Y-m-d H:i:s');
    }
}
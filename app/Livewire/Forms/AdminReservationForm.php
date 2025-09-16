<?php

namespace App\Livewire\Forms;

use App\Services\ReservationService;
use App\Services\ReservationValidationService;
use Livewire\Form;

class AdminReservationForm extends Form
{
    public ?int $userId = null;
    public ?int $entrepriseId = null;
    public int $passengerMode = ReservationService::EXIST_PASSAGER;
    public ?int $passengerId = null;
    public array $newPassager = [];
    public ?string $pickupDate = null;
    public int $pickupMode = ReservationService::WITH_PLACE;
    public ?int $localisationFromId = null;
    public ?int $addressReservationFrom = null;
    public array $newAdresseReservationFrom = [];
    public ?string $pickupOrigin = null;
    public bool $hasSteps = false;
    public ?string $steps = null;
    public int $dropMode = ReservationService::WITH_PLACE;
    public ?int $localisationToId = null;
    public ?string $dropOffOrigin = null;
    public ?int $addressReservationTo = null;
    public array $newAdresseReservationTo = [];
    public ?string $comment = null;

    protected function rules(): array
    {
        return array_merge(
            $this->defaultRules(),
            $this->getPassengerRules(),
            $this->getLocationRules(),
            $this->getStepsRules(),
        );
    }

    private function defaultRules(): array
    {
        return [
            'userId' => 'required|exists:users,id',
            'entrepriseId' => 'required|exists:entreprises,id',
            'pickupDate' => 'required|date_format:d/m/Y H:i',
            'hasSteps' => 'boolean',
            'comment' => 'nullable|string',
        ];
    }

    private function getPassengerRules(): array
    {
        if ($this->passengerMode === ReservationService::EXIST_PASSAGER) {
            return ['passengerId' => 'required|integer'];
        }

        if ($this->passengerMode === ReservationService::NEW_PASSAGER) {
            // Utilise les règles communes du service
            $commonRules = ReservationValidationService::getCommonPassengerRules();
            $rules = [];
            foreach ($commonRules as $field => $rule) {
                $rules["newPassager.{$field}"] = $rule;
            }

            // Validation cost center pour les entreprises spécifiques (admin)
            // En admin, on utilise l'entreprise de la secrétaire pour déterminer les exigences
            if (!is_null($this->entrepriseId) && ReservationValidationService::requiresCostCenter($this->entrepriseId)) {
                $costCenterRules = ReservationValidationService::getPassengerCorrectionRules();
                foreach ($costCenterRules as $field => $rule) {
                    $rules["newPassager.{$field}"] = $rule;
                }
            }

            return $rules;
        }

        return [];
    }

    /**
     * Règles de validation pour les localisations
     */
    private function getLocationRules(): array
    {
        $rules = [];

        // Règles de départ
        $rules = array_merge($rules, $this->getPickupRules());

        // Règles d'arrivée
        $rules = array_merge($rules, $this->getDropOffRules());

        return $rules;
    }

    /**
     * Règles de validation pour le lieu de départ
     */
    private function getPickupRules(): array
    {
        return match ($this->pickupMode) {
            ReservationService::WITH_PLACE => [
                'localisationFromId' => 'required|integer',
                'pickupOrigin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'addressReservationFrom' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => array_combine(
                array_map(fn($key) => "newAdresseReservationFrom.{$key}", array_keys(ReservationValidationService::getCommonAddressRules())),
                array_values(ReservationValidationService::getCommonAddressRules())
            ),
            default => []
        };
    }

    /**
     * Règles de validation pour le lieu d'arrivée
     */
    private function getDropOffRules(): array
    {
        return match ($this->dropMode) {
            ReservationService::WITH_PLACE => [
                'localisationToId' => 'required|integer',
                'dropOffOrigin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'addressReservationTo' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => array_combine(
                array_map(fn($key) => "newAdresseReservationTo.{$key}", array_keys(ReservationValidationService::getCommonAddressRules())),
                array_values(ReservationValidationService::getCommonAddressRules())
            ),
            default => []
        };
    }

    private function getStepsRules(): array
    {
        if ($this->hasSteps) {
            return [
                'steps' => 'required|array',
            ];
        }

        return [
            'steps' => 'nullable|string'
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            // Champs principaux
            'userId' => 'secrétaire',
            'entrepriseId' => 'entreprise rattachée',
            'passengerId' => 'passager',
            'pickupDate' => 'date de prise en charge',
            'comment' => 'commentaire',
            'hasSteps' => 'destinations intermédiaires',
            'steps' => 'destinations intermédiaires',

            // Nouveau passager
            'newPassager.nom' => 'nom et prénom',
            'newPassager.telephone' => 'téléphone de bureau',
            'newPassager.portable' => 'téléphone portable',
            'newPassager.email' => 'adresse email',
            'newPassager.cost_center_id' => 'cost center',
            'newPassager.type_facturation_id' => 'type de facturation',

            // Lieu de prise en charge
            'localisationFromId' => 'aéroports ou gares de départ',
            'pickupOrigin' => 'provenance / n°',
            'addressReservationFrom' => 'adresse de prise en charge',
            'newAdresseReservationFrom.adresse' => 'adresse de prise en charge',
            'newAdresseReservationFrom.adresseComplement' => 'adresse complémentaire de prise en charge',
            'newAdresseReservationFrom.codePostal' => 'code postal de prise en charge',
            'newAdresseReservationFrom.ville' => 'ville de prise en charge',

            // Lieu de destination
            'localisationToId' => 'aéroports ou gares de destination',
            'dropOffOrigin' => 'destination / n°',
            'addressReservationTo' => 'adresse de destination',
            'newAdresseReservationTo.adresse' => 'adresse de destination',
            'newAdresseReservationTo.adresse_complement' => 'adresse complémentaire de destination',
            'newAdresseReservationTo.codePostal' => 'code postal de destination',
            'newAdresseReservationTo.ville' => 'ville de destination',
        ];
    }

    public function createReservation(): void
    {
        $this->validate();
        $this->handleCreatePassenger();
    }
}

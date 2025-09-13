<?php

namespace App\Livewire\Forms;

use App\Models\Reservation;
use App\Models\Passager;
use App\Models\AdresseReservation;
use App\Services\ReservationService;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ReservationForm extends Form
{
    // User and Mode Selection  
    public ?string $userId = '';
    public int $passagerMode = ReservationService::EXIST_PASSAGER;
    public int $pickupMode = ReservationService::WITH_PLACE;
    public int $dropMode = ReservationService::WITH_PLACE;
    public int $backPickupMode = ReservationService::WITH_PLACE;
    public int $backDropMode = ReservationService::WITH_PLACE;
    public bool $hasBack = false;
    
    // Core Reservation
    public ?int $entreprise_id = null;
    public ?int $passager_id = null;
    
    public ?string $pickup_date = null;
    public ?string $commande = null;
    public ?string $comment = null;
    public bool $send_to_passager = true;
    public bool $calendar_passager_invitation = true;
    public bool $has_steps = false;
    public ?string $steps = null;
    
    // Locations
    public ?int $localisation_from_id = null;
    public ?string $pickup_origin = null;
    public ?int $localisation_to_id = null;
    public ?string $drop_off_origin = null;
    
    // Addresses
    public ?int $addressReservationFrom = null;
    public ?int $addressReservationTo = null;
    
    // Dynamic Data
    public array $newAdresseReservationFrom = [];
    public array $newAdresseReservationTo = [];
    public array $newPassager = [];
    public array $reservation_back = [];
    public array $newAdresseReservationFromBack = [];
    public array $newAdresseReservationToBack = [];
    
    // Error State
    public bool $ardianPassengerCostFacError = false;
    public ?Passager $passengerInError = null;
    
    // Validation Control
    public bool $useFormValidation = true;
    
    public function rules(): array
    {
        // If form validation is disabled (used with trait validation), return empty rules
        if (!$this->useFormValidation) {
            return [];
        }
        
        return array_merge(
            $this->getBaseRules(),
            $this->getPassengerRules(),
            $this->getLocationRules(),
            $this->getBackReservationRules()
        );
    }

    private function getBaseRules(): array
    {
        return [
            'userId' => 'nullable',
            'pickup_date' => 'required|date',
            'commande' => 'nullable|string',
            'comment' => 'nullable|string',
            'send_to_passager' => 'boolean',
            'calendar_passager_invitation' => 'boolean',
            'has_steps' => 'boolean',
            'steps' => 'nullable|string',
        ];
    }

    private function getPassengerRules(): array
    {
        if ($this->passagerMode === ReservationService::EXIST_PASSAGER) {
            return ['passager_id' => 'required|integer'];
        }
        
        if ($this->passagerMode === ReservationService::NEW_PASSAGER) {
            $rules = [
                'newPassager.nom' => 'required|string',
                'newPassager.email' => 'required|email',
                'newPassager.portable' => 'required|string',
                'newPassager.telephone' => 'nullable|string',
                'userId' => 'required',
            ];
            
            // Add cost center validation for specific companies
            if (!is_null($this->entreprise_id) && 
                in_array($this->entreprise_id, \app(\app\Settings\BillSettings::class)->entreprises_cost_center_facturation)) {
                $rules['newPassager.cost_center_id'] = 'required';
                $rules['newPassager.type_facturation_id'] = 'required';
            }
            
            return $rules;
        }
        
        return [];
    }

    private function getLocationRules(): array
    {
        $rules = [];
        
        // Pickup location rules
        $rules = array_merge($rules, $this->getPickupRules());
        
        // Drop-off location rules  
        $rules = array_merge($rules, $this->getDropOffRules());
        
        return $rules;
    }

    private function getPickupRules(): array
    {
        return match ($this->pickupMode) {
            ReservationService::WITH_PLACE => [
                'localisation_from_id' => 'required|integer',
                'pickup_origin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'addressReservationFrom' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => [
                'newAdresseReservationFrom.adresse' => 'required|string',
                'newAdresseReservationFrom.code_postal' => 'required|string',
                'newAdresseReservationFrom.ville' => 'required|string'
            ],
            default => []
        };
    }

    private function getDropOffRules(): array
    {
        return match ($this->dropMode) {
            ReservationService::WITH_PLACE => [
                'localisation_to_id' => 'required|integer',
                'drop_off_origin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'addressReservationTo' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => [
                'newAdresseReservationTo.adresse' => 'required|string',
                'newAdresseReservationTo.code_postal' => 'required|string',
                'newAdresseReservationTo.ville' => 'required|string'
            ],
            default => []
        };
    }

    private function getBackReservationRules(): array
    {
        if (!$this->hasBack) {
            return [];
        }

        $rules = [
            'reservation_back.pickup_date' => 'required|date',
            'reservation_back.comment' => 'nullable|string',
            'reservation_back.has_steps' => 'boolean',
            'reservation_back.steps' => 'nullable|string',
        ];

        // Back pickup rules
        $rules = array_merge($rules, match ($this->backPickupMode) {
            ReservationService::WITH_PLACE => [
                'reservation_back.localisation_from_id' => 'required|integer',
                'reservation_back.pickup_origin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'reservation_back.adresse_reservation_from_id' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => [
                'newAdresseReservationFromBack.adresse' => 'required|string',
                'newAdresseReservationFromBack.code_postal' => 'required|string',
                'newAdresseReservationFromBack.ville' => 'required|string'
            ],
            default => []
        });

        // Back drop-off rules
        $rules = array_merge($rules, match ($this->backDropMode) {
            ReservationService::WITH_PLACE => [
                'reservation_back.localisation_to_id' => 'required|integer',
                'reservation_back.drop_off_origin' => 'nullable|string'
            ],
            ReservationService::WITH_ADRESSE => [
                'reservation_back.adresse_reservation_to_id' => 'required|integer'
            ],
            ReservationService::WITH_NEW_ADRESSE => [
                'newAdresseReservationToBack.adresse' => 'required|string',
                'newAdresseReservationToBack.code_postal' => 'required|string',
                'newAdresseReservationToBack.ville' => 'required|string'
            ],
            default => []
        });

        return $rules;
    }
    
    public function resetDependentFields(): void
    {
        $this->resetMainFields();
        $this->resetAddressFields();
        $this->resetLocationFields();
        $this->resetErrorState();
        $this->resetModes();
    }

    private function resetMainFields(): void
    {
        $this->entreprise_id = null;
        $this->passager_id = null;
        $this->newPassager = [];
        $this->reservation_back = [];
    }

    private function resetAddressFields(): void
    {
        $this->addressReservationFrom = null;
        $this->addressReservationTo = null;
        $this->newAdresseReservationFrom = [];
        $this->newAdresseReservationTo = [];
        $this->newAdresseReservationFromBack = [];
        $this->newAdresseReservationToBack = [];
    }

    private function resetLocationFields(): void
    {
        $this->localisation_from_id = null;
        $this->pickup_origin = null;
        $this->localisation_to_id = null;
        $this->drop_off_origin = null;
    }

    private function resetErrorState(): void
    {
        $this->ardianPassengerCostFacError = false;
        $this->passengerInError = null;
    }

    private function resetModes(): void
    {
        $this->pickupMode = ReservationService::WITH_PLACE;
        $this->dropMode = ReservationService::WITH_PLACE;
        $this->backPickupMode = ReservationService::WITH_PLACE;
        $this->backDropMode = ReservationService::WITH_PLACE;
    }
    
    private function hasValue(string $array, string $key): bool
    {
        return isset($this->{$array}[$key]) && !empty($this->{$array}[$key]);
    }
}

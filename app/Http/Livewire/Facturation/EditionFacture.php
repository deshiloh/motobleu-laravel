<?php

namespace App\Http\Livewire\Facturation;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Events\BillCreated;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Validator;
use Livewire\Component;

class EditionFacture extends Component
{
    public $entrepriseId = null;
    public int $month = 0;
    public int $year = 0;
    public int $perPage = 10;
    public Reservation|null $currentReservation = null;
    public $email = null;
    public Facture $facture;

    // MODAL
    public bool $simpleModal = false;
    public bool $madeBillModal = false;

    private bool $isGenerateFacture = false;

    protected $queryString = [
        'entrepriseId' => ['except' => 0],
        'month',
        'year'
    ];

    public function mount()
    {
        $currentDate = Carbon::now();
        if ($this->month == 0) {
            $this->month = $currentDate->month;
        }

        if ($this->year == 0) {
            $this->year = $currentDate->year;
        }

        $this->facture = new Facture();
    }

    public function render()
    {
        return view('livewire.facturation.edition-facture')
            ->layout('components.admin-layout');
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getReservationsProperty()
    {
        $reservations = $this->generateQuery();

        return $reservations->paginate($this->perPage);
    }

    /**
     * @return Entreprise|null
     */
    public function getEntrepriseProperty()
    {
        return !empty($this->entrepriseId) ? Entreprise::find($this->entrepriseId) : null;
    }

    public function getAdresseFacturationEntrepriseProperty()
    {
        if ($this->entreprise === null) {
            return null;
        }

        return AdresseEntreprise::query()
            ->where('type', AdresseEntrepriseTypeEnum::FACTURATION)
            ->where('entreprise_id', $this->entrepriseId)
            ->get()->first();
    }

    protected function getRules()
    {
        if (!$this->isGenerateFacture) {
            return [
                'currentReservation.tarif' => 'required',
                'currentReservation.majoration' => 'nullable',
                'currentReservation.complement' => 'nullable',
                'currentReservation.comment_pilote' => 'nullable',
            ];
        }

        $this->facture->is_acquitte = false;

        return [
            'email.addressTo' => 'required|email',
            'email.message' => 'required',
            'facture.is_acquitte' => 'bool'
        ];
    }

    public function editItem(Reservation $item)
    {
        $this->currentReservation = $item;
        $this->simpleModal = true;
    }

    public function saveItem()
    {
        $this->isGenerateFacture = false;
        $this->validate();

        $this->currentReservation->saveQuietly();
        $this->simpleModal = false;
    }

    public function calculTotal(Reservation $reservation)
    {
        $total = floatval($reservation->tarif);
        $montantMajoration = $total * (floatval($reservation->majoration) / 100);
        $total = $total + $montantMajoration + floatval($reservation->complement);

        return $total;
    }

    public function openGenerateFactureModalAction()
    {
        $this->resetErrorBag();
        $this->email['addressTo'] = 'test@test.com';
        $this->email['message'] = 'Message';
        $this->madeBillModal = true;
    }

    public function generateFacture()
    {
        $this->isGenerateFacture = true;

        $reservations = $this->generateQuery()->get();

        $this->withValidator(function (Validator $validator) use ($reservations) {
            $validator->after(function ($validator) use ($reservations) {
                if (!$this->reservationsValidation($reservations)) {
                    $validator->errors()->add(null, "Toutes les réservations ne sont pas renseignées.");
                }
            });
        })->validate();



        BillCreated::dispatch($this->facture);
    }

    /**
     * Vérifie que les réservations sont renseignées.
     * @param Collection $reservations
     * @return bool
     */
    private function reservationsValidation(Collection $reservations){
        foreach ($reservations as $reservation) {
            if (!$this->calculTotal($reservation) > 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return Builder
     */
    private function generateQuery()
    {
        $reservations = Reservation::query()
            ->join('passagers', 'reservations.passager_id', 'passagers.id')
            ->join('users', 'passagers.user_id', 'users.id')
            ->join('entreprises', 'users.entreprise_id', 'entreprises.id')
            ->select('reservations.*')
            ->whereMonth('reservations.pickup_date', $this->month)
            ->whereYear('reservations.pickup_date', $this->year)
            ->where('reservations.is_confirmed', true)
        ;

        if ($this->entreprise) {
            $reservations->where('entreprises.id', $this->entreprise->id);
        }

        return $reservations;
    }
}

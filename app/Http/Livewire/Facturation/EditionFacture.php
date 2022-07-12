<?php

namespace App\Http\Livewire\Facturation;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Events\BillCreated;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use WireUi\Traits\Actions;

class EditionFacture extends Component
{
    use Actions;

    public int|null $selectedMonth = null;
    public int|null $selectedYear = null;
    public int|null $entrepriseIdSelected = null;
    public int|null $reservationSelected = null;

    public float $montant_ttc = 0;

    public bool $factureModal = false;
    public bool $reservationModal = false;
    public bool $isAcquitte = false;
    public bool $isFormFacture = false;

    public array $email;
    public array $months = [];
    public array $reservationFormData = [];

    protected $queryString = [
        'selectedMonth',
        'selectedYear',
        'entrepriseIdSelected'
    ];

    public function mount()
    {
        $currentDay = Carbon::now();

        $this->selectedMonth = $this->selectedMonth ?? $currentDay->month;
        $this->selectedYear = $this->selectedYear ?? $currentDay->year;

        $this->months = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
        ];

        $this->email['message'] = '';
    }

    public function render()
    {
        return view('livewire.facturation.edition-facture')
            ->layout('components.admin-layout');
    }

    /**
     * @return Builder[]|Collection
     */
    public function getEntreprisesProperty(): Collection|array
    {
        return Entreprise::query()
            ->join('users', 'entreprises.id', '=', 'users.entreprise_id')
            ->join('passagers', 'users.id', '=', 'passagers.user_id')
            ->join('reservations', 'passagers.id', '=', 'reservations.passager_id')
            ->select('entreprises.*', DB::raw('COUNT(reservations.id) as nbReservations'))
            ->whereMonth('reservations.pickup_date', $this->selectedMonth)
            ->whereYear('reservations.pickup_date', $this->selectedYear)
            ->where('reservations.is_billed', false)
            ->groupBy('entreprises.id')
            ->get();
    }

    /**
     * @return Builder[]|Collection|null
     */
    public function getReservationsProperty(): Collection|array|null
    {
        if (!$this->entrepriseIdSelected) {
            return null;
        }

        return Reservation::query()
            ->join('passagers', 'reservations.passager_id', '=', 'passagers.id')
            ->join('users', 'passagers.user_id', '=', 'users.id')
            ->join('entreprises', 'users.entreprise_id', '=', 'entreprises.id')
            ->select('reservations.*')
            ->whereMonth('reservations.pickup_date', $this->selectedMonth)
            ->whereYear('reservations.pickup_date', $this->selectedYear)
            ->where('entreprises.id', $this->entrepriseIdSelected)
            ->where('reservations.is_billed', false)
            ->get();
    }

    /**
     * @return Entreprise|null
     */
    public function getEntrepriseProperty(): ?Entreprise
    {
        if (!$this->entrepriseIdSelected) {
            return null;
        }

        return Entreprise::find($this->entrepriseIdSelected);
    }

    /**
     * @return Facture|null
     */
    public function getFactureProperty(): ?Facture
    {
        if (!$this->entrepriseIdSelected) {
            return null;
        }

        return $this->getFactureFor($this->selectedMonth, $this->selectedYear, $this->entrepriseIdSelected);
    }

    public function getReservationProperty()
    {
        if (!$this->reservationSelected) {
            return null;
        }

        return Reservation::find($this->reservationSelected);
    }

    /**
     * @return AdresseEntreprise|null
     */
    public function getAdresseFacturationEntrepriseProperty(): ?AdresseEntreprise
    {
        if ($this->entreprise === null) {
            return null;
        }

        return AdresseEntreprise::query()
            ->where('type', AdresseEntrepriseTypeEnum::FACTURATION)
            ->where('entreprise_id', $this->entreprise->id)
            ->get()->first();
    }

    /**
     * @param int $month
     * @param int $year
     * @param int $entreprise
     * @return bool
     */
    public function isFactureExist(int $month, int $year, int $entreprise): bool
    {
        $query = Facture::query()
            ->join('reservations', 'factures.id', '=', 'reservations.facture_id')
            ->join('passagers', 'reservations.passager_id', '=', 'passagers.id')
            ->join('users', 'users.id', '=', 'passagers.user_id')
            ->join('entreprises', 'users.entreprise_id', '=', 'entreprises.id')
            ->where('entreprises.id', $entreprise)
            ->whereMonth('reservations.pickup_date', $month)
            ->whereYear('reservations.pickup_date', $year)
            ->count();
        return $query > 0;
    }

    /**
     * @param int $month
     * @param int $year
     * @param int $entreprise
     * @return Facture
     */
    public function getFactureFor(int $month, int $year, int $entreprise): Facture
    {
        if ($this->isFactureExist($this->selectedMonth, $this->selectedYear, $this->entrepriseIdSelected)) {
            $facture = Facture::query()
                ->join('reservations', 'factures.id', '=', 'reservations.facture_id')
                ->join('passagers', 'reservations.passager_id', '=', 'passagers.id')
                ->join('users', 'users.id', '=', 'passagers.user_id')
                ->join('entreprises', 'users.entreprise_id', '=', 'entreprises.id')
                ->select('factures.*')
                ->where('entreprises.id', $entreprise)
                ->whereMonth('reservations.pickup_date', $month)
                ->whereYear('reservations.pickup_date', $year)
                ->get()
                ->first();
        } else {
            $facture = Facture::create([
                'month' => $this->selectedMonth,
                'year' => $this->selectedYear
            ]);
        }

        if (!empty($this->reservations)) {
            foreach ($this->reservations as $reservation) {
                if (!$reservation->facture_id) {
                    $reservation->updateQuietly([
                        'facture_id' => $facture->id
                    ]);
                }
            }
        }

        return $facture;
    }

    /**
     * @param int $entreprise
     * @return void
     */
    public function goToEditPage(int $entreprise): void
    {
        $this->entrepriseIdSelected = $entreprise;
    }

    /**
     * @return void
     */
    public function sendFactureModal(): void
    {
        $this->resetErrorBag();

        $this->email['address'] = 'test@test.com';
        $this->email['message'] = sprintf("Bonjour, <br> <br> Veuillez trouver ci-joint la facture %s et le récapitulatif des courses pour la période de %s %s. <br> <br> Cordialement",
            $this->facture->reference,
            $this->months[$this->facture->month],
            $this->facture->year
        );

        $this->factureModal = true;
    }

    /**
     * @return string[]
     */
    public function getRules(): array
    {
        if ($this->factureModal) {
            return [
                'email.address' => 'required|email',
                'email.message' => 'required',
                'isAcquitte' => 'bool'
            ];
        }

        return [
            'reservationFormData.tarif' => 'required|numeric',
            'reservationFormData.majoration' => 'nullable',
            'reservationFormData.complement' => 'nullable',
            'reservationFormData.comment_pilote' => 'nullable'
        ];
    }

    /**
     * @param int $reservationId
     * @return void
     */
    public function reservationModal(int $reservationId): void
    {
        $this->resetErrorBag();

        $this->reservationSelected = $reservationId;

        $this->reservationFormData['tarif'] = $this->reservation->tarif;
        $this->reservationFormData['majoration'] = $this->reservation->majoration;
        $this->reservationFormData['complement'] = $this->reservation->complement;
        $this->reservationFormData['comment_pilote'] = $this->reservation->comment_pilote;

        $this->reservationModal = true;
    }

    /**
     * @return void
     */
    public function sendFactureAction(): void
    {
        $this->validate();
        $this->factureModal = false;

        foreach ($this->reservations as $reservation) {
            $reservation->updateQuietly([
                'is_billed' => true
            ]);
        }

        BillCreated::dispatch($this->facture);

        $this->notification()->success(
            'Facture envoyée avec succés.'
        );
    }

    /**
     * @return void
     */
    public function saveReservationAction(): void
    {
        $this->validate();
        $this->reservation->updateQuietly($this->reservationFormData);
        $this->reservationModal = false;
        $this->notification()->success(
            'Opération réussite',
            'La valeur de la réservation a bien été sauvegardée.'
        );
    }

    /**
     * @param Reservation $reservation
     * @return float|int
     */
    public function calculTotal(Reservation $reservation): float|int
    {
        $total = floatval($reservation->tarif);
        $montantMajoration = $total * (floatval($reservation->majoration) / 100);
        $total = $total + $montantMajoration + floatval($reservation->complement);

        return $total;
    }
}

<?php

namespace App\Http\Livewire\Facturation;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Enum\ReservationStatus;
use App\Events\BillCreated;
use App\Exports\ReservationsExport;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Reservation;
use App\Services\ExportService;
use app\Settings\BillSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Validator;
use Livewire\Component;
use Livewire\Redirector;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use WireUi\Traits\Actions;

class EditionFacture extends Component
{
    use Actions;

    public int|null $selectedMonth = null;
    public int|null $selectedYear = null;
    public int|null $entrepriseIdSelected = null;
    public int|null $entrepriseSearch = null;
    public string $uniqID;

    public float $montant_ttc = 0;

    public bool $factureModal = false;
    public bool $isAcquitte = false;
    public bool $isFormFacture = false;
    public ?int $isBilled = null;

    public array $email;
    public array $months = [];
    public array $reservationFormData = [];

    /**
     * @var string[]
     */
    protected $queryString = [
        'selectedMonth',
        'selectedYear',
        'entrepriseIdSelected',
        'entrepriseSearch'
    ];

    /**
     * @var string[]
     */
    protected $listeners = ['reservationUpdated', 'editReservation'];

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

        $this->uniqID = uniqid('facture_');

        if ($this->facture !== null) {
            $this->isAcquitte = $this->facture->is_acquitte;
        }
    }

    /**
     * @return mixed
     */
    public function render(): mixed
    {
        return view('livewire.facturation.edition-facture')
            ->layout('components.layout');
    }

    /**
     * @return Builder[]|Collection
     */
    public function getEntreprisesProperty(): Collection|array
    {
        return Entreprise::withCount([
            'reservations' => function(Builder $query) {
                $query
                    ->whereMonth('pickup_date', $this->selectedMonth)
                    ->whereYear('pickup_date', $this->selectedYear)
                    ->whereIn('statut', [ReservationStatus::Confirmed->value, ReservationStatus::CanceledToPay->value])
                    ->where('encompte_pilote', '>', 0)
                ;
            }]
        )
            ->whereHas('reservations', function (Builder $query) {
                $query
                    ->whereMonth('pickup_date', $this->selectedMonth)
                    ->whereYear('pickup_date', $this->selectedYear)
                    ->whereIn('statut', [ReservationStatus::Confirmed->value, ReservationStatus::CanceledToPay->value])
                    ->where('encompte_pilote', '>', 0)
                ;
            })
            ->when($this->entrepriseSearch != null, function(Builder $query) {
                $query->where('id', $this->entrepriseSearch);
            })
            ->orderBy('nom')
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

        return Reservation::where('entreprise_id', $this->entrepriseIdSelected)
            ->whereMonth('pickup_date', $this->selectedMonth)
            ->whereYear('pickup_date', $this->selectedYear)
            ->where('encompte_pilote', '>', 0)
            ->whereIn('statut', [ReservationStatus::Confirmed->value, ReservationStatus::CanceledToPay->value])
            ->orderBy('pickup_date', 'desc')
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

    /**
     * @return AdresseEntreprise|null
     */
    public function getAdresseFacturationEntrepriseProperty(): Model|null
    {
        if ($this->entreprise === null) {
            return null;
        }

        return AdresseEntreprise::where('type', AdresseEntrepriseTypeEnum::FACTURATION)
            ->where('entreprise_id', $this->entreprise->id)
            ->first();
    }

    /**
     * @return Builder|Model|null
     */
    public function getAdresseEntrepriseProperty(): Builder|Model|null
    {
        if ($this->entreprise === null) {
            return null;
        }

        return AdresseEntreprise::where('type', AdresseEntrepriseTypeEnum::PHYSIQUE)
            ->where('entreprise_id', $this->entreprise->id)
            ->first();
    }

    /**
     * @param int $month
     * @param int $year
     * @param int $entrepriseId
     * @return bool
     */
    public function isFactureExist(int $month, int $year, int $entrepriseId): bool
    {
        $query = Facture::where('month', $month)
            ->where('year', $year)
            ->whereHas('reservations', function (Builder $query) use ($entrepriseId) {
                $query->where('entreprise_id', $entrepriseId);
            })->count();

        return $query > 0;
    }

    /**
     * @param int $month
     * @param int $year
     * @param int $entrepriseId
     * @return Facture
     */
    public function getFactureFor(int $month, int $year, int $entrepriseId): Facture
    {
        if ($this->isFactureExist($this->selectedMonth, $this->selectedYear, $this->entrepriseIdSelected)) {
            // Récupération de la facture existante
            $facture = Facture::where('month', $month)
                ->where('year', $year)
                ->whereHas('reservations', function (Builder $query) use ($entrepriseId){
                    $query->where('entreprise_id', $entrepriseId);
                })
                ->first();
        } else {
            // Génération de l'adresse de la nouvelle facture
            $addressFacturation = (is_null($this->adresseFacturationEntreprise)) ?
                $this->adresseEntreprise->address_bill_format :
                $this->adresseFacturationEntreprise->address_bill_format;

            $addressLocalEntreprise = (is_null($this->adresseEntreprise)) ?
                $this->adresseFacturationEntreprise->address_bill_format :
                $this->adresseEntreprise->address_bill_format;

            // Génération de la référence de la facture
            $reference = Facture::generateReference($this->selectedYear, $this->selectedMonth);

            // Création de la nouvelle facture
            $facture = Facture::create([
                'reference' => $reference,
                'month' => $this->selectedMonth,
                'year' => $this->selectedYear,
                'adresse_client' => $addressLocalEntreprise,
                'adresse_facturation' => $addressFacturation
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

        $this->email['address'] = $this->adresse_facturation_entreprise->email;
        $this->email['message'] = sprintf("Bonjour, <br> <br> Veuillez trouver ci-joint la facture %s et le récapitulatif des courses pour la période de %s %s.",
            $this->facture->reference,
            $this->months[$this->facture->month],
            $this->facture->year
        );
        $this->email['complement'] = $this->facture->information;
        $this->isAcquitte = (int) $this->facture->is_acquitte;

        $this->factureModal = true;
    }

    /**
     * @return string[]
     */
    public function getRules(): array
    {
        return [
            'email.address' => 'required|email',
            'email.message' => 'required',
            'email.complement' => 'nullable',
            'isAcquitte' => 'bool'
        ];
    }

    /**
     * @return void
     */
    public function sendFactureAction(): void
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {
                /** @var Reservation $reservation */
                foreach ($this->reservations as $reservation) {
                    if ($reservation->tarif === null) {
                        $validator->errors()->add('réservations', 'Vous devez éditer toutes les réservations');
                    }
                }
            });
        })->validate();

        $this->facture->information = $this->email['complement'];

        foreach ($this->reservations as $reservation) {
            $reservation->statut = ReservationStatus::Billed;
            $reservation->updateQuietly();
        }

        BillCreated::dispatch($this->facture, $this->email);

        $this->notification([
            'title' => 'Facture envoyée.',
            'description' => 'Vous allez être redirigé vers la page de listing entreprises',
            'icon' => 'success',
            'onTimeout' => [
                'method' => 'redirectEvent',
            ],
        ]);

        $this->factureModal = false;
    }

    public function redirectEvent()
    {
        return redirect()->to(route('admin.facturations.edition', [
                'selectedMonth' => $this->selectedMonth,
                '$selectedYear' => $this->selectedYear
            ]
        ));
    }

    public function sendEmailTestAction()
    {
        $this->facture->information = $this->email['complement'];

        Mail::to(config('mail.admin.address'))
            ->send(new \App\Mail\BillCreated($this->facture, $this->email['message']));
    }

    /**
     * @param Reservation $reservation
     * @return float|int|null
     */
    public function calculTotal(Reservation $reservation): float|int|null
    {
        if ($reservation->tarif == null) {
            return null;
        }

        $total = floatval($reservation->tarif);
        $montantMajoration = $total * (floatval($reservation->majoration) / 100);

        return $total + $montantMajoration + floatval($reservation->complement);
    }

    public function reservationUpdated()
    {
        $total_ttc = 0;

        foreach ($this->reservations as $reservation) {
            $currentTotalTTC = $this->calculTotal($reservation);

            if ($currentTotalTTC > 0) {
                $total_ttc = $total_ttc + $currentTotalTTC;
            }
        }

        $total_ht = $total_ttc / 1.10;

        $this->facture->updateQuietly([
            'montant_ht' => $total_ht
        ]);

        $this->uniqID = uniqid('facture_');
    }

    public function editReservation(array $datas): bool
    {
        $validator = \Illuminate\Support\Facades\Validator::make($datas, [
            'tarif' => 'required',
            'majoration' => 'nullable',
            'complement' => 'nullable',
            'comment_facture' => 'nullable'
        ]);

        if ($validator->fails()) {
            $this->notification()->error('Erreur', implode('', $validator->errors()->all()));
            return false;
        }

        $reservation = Reservation::find($datas['reservation']);
        $reservation->updateQuietly([
            'tarif' => $datas['tarif'],
            'majoration' => $datas['majoration'],
            'complement' => $datas['complement'],
            'comment_facture' => $datas['comment_facture'],
        ]);

        $this->emit('reservationUpdated');

        $this->notification()->success('Opération réussite', 'Modifications correctement effectuées');

        return true;
    }

    public function editFactureAction()
    {
        $this->facture->updateQuietly([
            'is_acquitte' => $this->isAcquitte,
            'information' => $this->email['complement']
        ]);
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportAction(BillSettings $billSettings)
    {
        if (in_array($this->entreprise->id, $billSettings->entreprises_xls_file)) {
            // Export en XLS
            return Excel::download(new ReservationsExport($this->selectedYear, $this->selectedMonth, $this->entreprise), 'reservations.xlsx');
        } else {
            // Export PDF
            return response()->streamDownload(function () {
                echo Pdf::loadView('exports.reservations.pdf-facture', [
                    'entreprise' => $this->entreprise,
                    'year' => $this->facture->year,
                    'month' => $this->facture->month
                ])->output();
            }, 'recap_reservations_' . $this->selectedMonth . '_' . $this->selectedYear . '.pdf');
        }
    }

    public function updateAcquitteBill(): void
    {
        if ($this->facture !== null) {
            $this->facture->updateQuietly([
                'is_acquitte' => $this->isAcquitte
            ]);

            $this->emit('reservationUpdated');

            $this->notification()->success('Opération réussite', 'Modifications correctement effectuées');
        }
    }
}

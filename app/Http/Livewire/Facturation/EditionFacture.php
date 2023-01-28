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
    public int|null $reservationSelected = null;
    public string $uniqID;

    public float $montant_ttc = 0;

    public bool $factureModal = false;
    public bool $reservationModal = false;
    public bool $isAcquitte = false;
    public bool $isFormFacture = false;
    public int|null $isBilled = null;

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
        'isBilled'
    ];

    /**
     * @var string[]
     */
    protected $listeners = ['reservationUpdated'];

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
                    ->where('statut',ReservationStatus::Confirmed)
                    ->orWhere('statut', ReservationStatus::CanceledToPay)
                ;
            }]
        )
            ->whereHas('reservations', function (Builder $query) {
                $query
                    ->whereMonth('pickup_date', $this->selectedMonth)
                    ->whereYear('pickup_date', $this->selectedYear)
                    ->where('statut',ReservationStatus::Confirmed)
                    ->orWhere('statut', ReservationStatus::CanceledToPay)
                ;
            })
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
            ->where('statut',ReservationStatus::Confirmed)
            ->orWhere('statut', ReservationStatus::CanceledToPay)
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
    public function getAdresseFacturationEntrepriseProperty(): Model|null
    {
        if ($this->entreprise === null) {
            return null;
        }

        return AdresseEntreprise::query()
            ->where('type', AdresseEntrepriseTypeEnum::FACTURATION)
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

        return AdresseEntreprise::query()
            ->where('type', AdresseEntrepriseTypeEnum::PHYSIQUE)
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
                $this->adresseEntreprise->adresse_full :
                $this->adresseFacturationEntreprise->adresse_full;

            // Génération de la référence de la facture
            $reference = sprintf('FA%s-%s-%s',
                $this->selectedYear,
                $this->selectedMonth,
                Facture::where('month', $this->selectedMonth)->where('year', $this->selectedYear)->count() + 1
            );

            // Création de la nouvelle facture
            $facture = Facture::create([
                'reference' => $reference,
                'month' => $this->selectedMonth,
                'year' => $this->selectedYear,
                'adresse_client' => $this->adresseEntreprise->adresse_full,
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
        $this->email['message'] = sprintf("Bonjour, <br> <br> Veuillez trouver ci-joint la facture %s et le récapitulatif des courses pour la période de %s %s. <br> <br> Cordialement",
            $this->facture->reference,
            $this->months[$this->facture->month],
            $this->facture->year
        );
        $this->email['complement'] = '';
        $this->isAcquitte = (int) $this->facture->is_acquitte;

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
                'email.complement' => 'nullable',
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
    public function sendFactureAction()
    {
        $this->withValidator(function (Validator $validator) {
            $validator->after(function ($validator) {
                /** @var Reservation $reservation */
                foreach ($this->reservations as $reservation) {
                    if ($reservation->tarif == 0) {
                        $validator->errors()->add('réservations', 'Vous devez éditer toutes les réservations');
                    }
                }
            });
        })->validate();

        foreach ($this->reservations as $reservation) {
            $reservation->statut = ReservationStatus::Billed;
            $reservation->updateQuietly();
        }

        BillCreated::dispatch($this->facture, $this->email);

        $this->notification([
            'title' => 'Facture envoyée avec succés.',
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

    /**
     * @return void
     */
    public function saveReservationAction(): void
    {
        $this->validate();
        $this->reservation->updateQuietly($this->reservationFormData);
        $this->reservationModal = false;
        $this->emit('reservationUpdated');
        $this->notification()->success(
            'Opération réussite',
            'La valeur de la réservation a bien été sauvegardée.'
        );
    }

    public function sendEmailTestAction()
    {
        Mail::to(config('mail.admin.address'))
            ->send(new \App\Mail\BillCreated($this->facture, $this->email['message']));
    }

    /**
     * @param Reservation $reservation
     * @return float|int
     */
    public function calculTotal(Reservation $reservation): float|int
    {
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
}

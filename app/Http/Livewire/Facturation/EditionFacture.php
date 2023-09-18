<?php

namespace App\Http\Livewire\Facturation;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Enum\BillStatut;
use App\Enum\ReservationStatus;
use App\Events\BillCreated;
use App\Exports\ReservationsExport;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Reservation;
use app\Settings\BillSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Validator;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use WireUi\Traits\Actions;

class EditionFacture extends Component
{
    use Actions;

    public ?int $selectedMonth = null;
    public ?int $selectedYear = null;
    public ?int $entrepriseSearch = null;
    public string $uniqID;

    public float $montant_ttc = 0;

    public bool $isSendFactureModalOpened = false;

    public array $email;
    public array $months = [];
    public array $reservationFormData = [];
    public ?Facture $facture = null;
    public ?int $factureSelected = null;
    public ?Entreprise $entreprise = null;
    public bool $isAcquitte = false;

    /**
     * @var string[]
     */
    protected $queryString = [
        'selectedMonth',
        'selectedYear',
        'entrepriseSearch',
        'factureSelected' => ['except' => 0]
    ];

    /**
     * @var string[]
     */
    protected $listeners = ['reservationUpdated', 'editReservation'];

    /**
     * @return string[]
     */
    public function getRules(): array
    {
        $rules = [
            'email.address' => 'required|email',
            'email.message' => 'required',
            'email.complement' => 'nullable',
            'isAcquitte' => 'bool'
        ];

        if (null !== $this->facture) {
            $rules['facture.is_acquitte'] = 'boolean';
            $rules['facture.information'] = 'nullable';
        }

        return $rules;
    }

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

        if ($this->factureSelected) {
            $this->facture = Facture::findOrFail($this->factureSelected);
            $this->isAcquitte = $this->facture->is_acquitte;
            $this->entreprise = $this->facture->reservations->first()->entreprise ?? null;
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
        return Entreprise::orderBy('nom')
            ->whereHas('reservations', function (Builder $query) {
                $query
                    ->whereMonth('pickup_date', $this->selectedMonth)
                    ->whereYear('pickup_date', $this->selectedYear)
                    ->whereIn('statut', [ReservationStatus::Confirmed->value, ReservationStatus::CanceledToPay->value])
                    ->where(function(Builder $query) {
                        $query
                            ->whereNull('encaisse_pilote')
                            ->orWhere('encaisse_pilote', 0);
                    })
                ;
            })
            ->withCount([
                'reservations' => function(Builder $query) {
                    $query
                        ->whereMonth('pickup_date', $this->selectedMonth)
                        ->whereYear('pickup_date', $this->selectedYear)
                        ->whereIn('statut', [ReservationStatus::Confirmed->value, ReservationStatus::CanceledToPay->value])
                        ->where(function(Builder $query) {
                            $query
                                ->whereNull('encaisse_pilote')
                                ->orWhere('encaisse_pilote', 0);
                        })
                    ;
                }]
            )
            ->when($this->entrepriseSearch, function(Builder $query) {
                $query->where('id', $this->entrepriseSearch);
            })
            ->get();
    }

    /**
     * Permet de récupérer l'entreprise de la facture
     * @return Entreprise|null
     */
    public function getEntrepriseProperty(): ?Entreprise
    {
        if ($this->facture === null) {
            return null;
        }

        $entrepriseId = $this->facture->reservations->first()->entreprise_id;
        return Entreprise::find($entrepriseId);
    }

    public function goToEditPage(int $entrepriseId): void
    {
        $reservations = Reservation::where('entreprise_id', $entrepriseId)
            ->whereMonth('pickup_date', $this->selectedMonth)
            ->whereYear('pickup_date', $this->selectedYear)
            ->whereIn('statut', [
                ReservationStatus::Confirmed->value,
                ReservationStatus::CanceledToPay->value
            ])
            ->where(function(Builder $query) {
                $query
                    ->whereNull('encaisse_pilote')
                    ->orWhere('encaisse_pilote', 0);
            })
            ->get();

        // Génère ou récupère une facture
        $facture = $this->getExistFacture($entrepriseId);

        if ($facture === null) {
            $facture = $this->generateFacture($entrepriseId);
        }

        if ($facture && $facture->statut == BillStatut::COMPLETED) {
            $facture = $this->generateFacture($entrepriseId);
        }

        foreach ($reservations as $reservation) {
            if ($reservation->facture_id === null) {
                $reservation->updateQuietly([
                    'facture_id' => $facture->id
                ]);
            }
        }

        $this->facture = $facture;
        $this->isAcquitte = (boolean) $facture->is_acquitte;
        $this->facture->refresh();
        $this->factureSelected = $facture->id;
        $this->entreprise = Entreprise::findOrFail($entrepriseId);
    }

    public function getExistFacture(int $entrepriseSelected): ?Facture
    {
        return Facture::where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->whereHas('reservations', function(Builder $query) use ($entrepriseSelected) {
                return $query->where('entreprise_id', $entrepriseSelected);
            })
            ->where('is_acquitte', false)
            ->where('statut', BillStatut::CREATED->value)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Génère une facture
     * @param int $entrepriseId
     * @return Facture
     */
    public function generateFacture(int $entrepriseId): Facture
    {
        $addressBillEntreprise = AdresseEntreprise::where('type', AdresseEntrepriseTypeEnum::FACTURATION)
            ->where('entreprise_id', $entrepriseId)
            ->first();

        $addressLocalEntreprise = AdresseEntreprise::where('type', AdresseEntrepriseTypeEnum::PHYSIQUE)
            ->where('entreprise_id', $entrepriseId)
            ->first();

        // Génération de l'adresse de la nouvelle facture
        $addressFacturation = (is_null($addressBillEntreprise)) ?
            $addressLocalEntreprise->address_bill_format :
            $addressBillEntreprise->address_bill_format;

        $addressLocalFacturation = (is_null($addressLocalEntreprise)) ?
            $addressBillEntreprise->address_bill_format :
            $addressLocalEntreprise->address_bill_format;

        // Génération de la référence de la facture
        $reference = Facture::generateReference($this->selectedYear, $this->selectedMonth);

        // Création de la nouvelle facture
        return Facture::create([
            'reference' => $reference,
            'month' => $this->selectedMonth,
            'year' => $this->selectedYear,
            'adresse_client' => $addressLocalFacturation,
            'adresse_facturation' => $addressFacturation
        ]);
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

    /**
     * @param array $datas
     * @return bool
     */
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
            'tarif' => (float) $datas['tarif'],
            'majoration' => (float) $datas['majoration'],
            'complement' => (float) $datas['complement'],
            'comment_facture' => $datas['comment_facture'],
        ]);

        $this->emit('reservationUpdated');

        $this->notification()->success('Opération réussite', 'Modifications correctement effectuées');

        return true;
    }

    /**
     * @return void
     */
    public function reservationUpdated(): void
    {
        $total_ttc = 0;

        foreach ($this->facture->reservations as $reservation) {
            $currentTotalTTC = $this->calculTotal($reservation);

            if ($currentTotalTTC > 0) {
                $total_ttc = $total_ttc + $currentTotalTTC;
            }
        }

        $this->facture->updateQuietly([
            'montant_ttc' => $total_ttc
        ]);

        $this->uniqID = uniqid('facture_');
    }

    /**
     * Permet d'ouvrir la modal pour l'envoi de la facture
     * @return void
     */
    public function openSendFactureModal(): void
    {
        $addressBillEntreprise = $this->entreprise->getBilledAddress();

        $this->email['address'] = $addressBillEntreprise->email;
        $this->email['message'] = sprintf("Bonjour, <br> <br> Veuillez trouver ci-joint la facture %s et le récapitulatif des courses pour la période de %s %s.",
            $this->facture->reference,
            $this->months[$this->facture->month],
            $this->facture->year
        );
        $this->email['complement'] = $this->facture->information;

        $this->isSendFactureModalOpened = true;
    }

    /**
     * Export le récap des courses avant envoi
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportAction(BillSettings $billSettings)
    {
        if (in_array($this->entreprise->id, $billSettings->entreprises_xls_file)) {
            // Export en XLS
            return Excel::download(
                new ReservationsExport(
                    $this->selectedYear,
                    $this->selectedMonth,
                    $this->entreprise,
                    $this->factureSelected
                ),
                'reservations.xlsx');
        } else {
            // Export PDF
            return response()->streamDownload(function () {
                $facture = Facture::find($this->factureSelected);

                echo Pdf::loadView('exports.reservations.pdf-facture', [
                    'entreprise' => $this->entreprise,
                    'year' => $this->facture->year,
                    'month' => $this->facture->month,
                    'factureSelected' => $facture
                ])->output();
            }, 'recap_reservations_' . $this->selectedMonth . '_' . $this->selectedYear . '.pdf');
        }
    }

    /**
     * Event de mise à jour du statut is_acquitte de la facture
     * @return void
     */
    public function updateAcquitteBill(): void
    {
        $this->facture->updateQuietly([
            'is_acquitte' => !$this->facture->is_acquitte
        ]);

        $this->uniqID = uniqid('facture_');

        $this->notification([
            'title' => 'Opération réussite',
            'description' => 'Modification correctement effectuée',
            'icon' => 'success',
            'onClose' => [
                'method' => 'redirectFacturationList'
            ],
            'timeout' => 3000
        ]);
    }

    public function redirectFacturationList()
    {
        $this->redirect(route('admin.facturations.index'));
    }

    /**
     * Change les informations issue du formulaire email
     * @return void
     */
    public function editFactureAction(): void
    {
        $this->facture->updateQuietly([
            'information' => $this->email['complement']
        ]);

        $this->uniqID = uniqid('facture_');
    }

    /**
     * Redirect vers la liste des entreprises à facturer
     */
    public function redirectEvent()
    {
        if ($this->facture->statut == BillStatut::COMPLETED) {
            return redirect()->to(route('admin.facturations.index'));

        } else {
            return redirect()->to(route('admin.facturations.edition', [
                    'selectedMonth' => $this->selectedMonth,
                    '$selectedYear' => $this->selectedYear
                ]
            ));
        }
    }

    /**
     * Finalise et envoi la facture
     * @return void
     */
    public function sendFactureAction(): void
    {
        if ($this->facture->statut != BillStatut::COMPLETED) {
            $this->withValidator(function (Validator $validator) {
                $validator->after(function ($validator) {
                    /** @var Reservation $reservation */
                    foreach ($this->facture->reservations as $reservation) {
                        if ($reservation->tarif === null) {
                            $validator->errors()->add('reservation', 'Vous devez éditer toutes les réservations');
                        }
                    }
                });
            })->validate();

            foreach ($this->facture->reservations as $reservation) {
                $reservation->statut = ReservationStatus::Billed;
                $reservation->updateQuietly();
            }
        }


        $this->facture->updateQuietly([
            'statut' => BillStatut::COMPLETED->value
        ]);

        BillCreated::dispatch($this->facture, $this->email);

        $this->notification([
            'title' => 'Facture envoyée.',
            'description' => 'Vous allez être redirigé vers la page de listing entreprises',
            'icon' => 'success',
            'onTimeout' => [
                'method' => 'redirectEvent',
            ],
        ]);

        $this->isSendFactureModalOpened = false;
    }

    /**
     * Envoi d'un email de test pour vérifier le mail d'envoi de facture
     * @return void
     */
    public function sendEmailTestAction(): void
    {
        Mail::to(config('mail.admin.address'))
            ->send(new \App\Mail\BillCreated($this->facture, $this->email['message']));
    }
}

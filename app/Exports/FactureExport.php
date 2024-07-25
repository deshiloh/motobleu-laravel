<?php

namespace App\Exports;

use App\Enum\BillStatut;
use App\Models\Facture;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class FactureExport implements FromCollection, WithMapping, withHeadings, ShouldAutoSize, WithEvents, WithColumnFormatting
{
    private ?string $dateDebut;
    private ?string $dateFin;
    private ?int $entreprise;
    private $factures;
    private string $colValues;


    /**
     * @param string|null $dateDebut
     * @param string|null $dateFin
     * @param ?int $entreprise
     */
    public function __construct(?string $dateDebut, ?string $dateFin, ?int $entreprise)
    {
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->entreprise = $entreprise;
        $this->colValues = 'E';

        $this->factures = Facture::has('reservations')
            ->whereIn('statut', [BillStatut::COMPLETED, BillStatut::CANCEL])
            ->orderBy('id', 'DESC')
            ->when($this->dateDebut && $this->dateFin, function(Builder $query)  {
                $dateDebut = Carbon::createFromFormat("Y-m-d", $this->dateDebut);
                $dateFin = Carbon::createFromFormat("Y-m-d", $this->dateFin);

                $months = [];
                $years = [];

                for ($currentMonth = $dateDebut->month; $currentMonth <= $dateFin->month; $currentMonth ++) {
                    $months[] = $currentMonth;
                }

                for ($currentYear = $dateDebut->year; $currentYear <= $dateFin->year; $currentYear ++) {
                    $years[] = $currentYear;
                }


                return $query
                    ->whereIn('year', $years)
                    ->whereIn('month', $months);
            })
            ->when($this->entreprise, function(Builder $query) {
                return $query->whereHas('reservations', function (Builder $query) {
                    return $query->where('entreprise_id', $this->entreprise);
                });
            })->get();
    }


    public function collection()
    {
        return $this->factures;
    }

    /**
     * @param $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->reference,
            $row->created_at->format('d/m/Y'),
            $row->is_acquitte ? 'Oui' : 'Non',
            $row->reservations->first()->entreprise->nom  ?? "Non disponible",
            $row->montant_ttc
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Référence',
            'Date Création',
            'Acquittée',
            'Entreprise',
            'Montant'
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $sheet) {
                $sousTotalIndex = 3;
                $tvaIndex = 4;
                $totalIndex = 5;

                $sheet->getSheet()->getCell('D' . $this->factures->count() + $sousTotalIndex)
                    ->setValue('Sous total');
                $sheet->getSheet()->getCell('E' . $this->factures->count() + $sousTotalIndex)
                    ->setValue(
                        sprintf(
                            '=(%s/1.1)',
                            'E' . $this->factures->count() + $totalIndex
                        )
                    );

                $sheet->getSheet()->getCell('D' . $this->factures->count() + $tvaIndex)
                    ->setValue('TVA (10%)');
                $sheet->getSheet()->getCell('E' . $this->factures->count() + $tvaIndex)
                    ->setValue(sprintf(
                        '=(%s-%s)',
                        'E' . $this->factures->count() + $totalIndex,
                        'E' . $this->factures->count() + $sousTotalIndex
                    ));

                $sheet->getSheet()->getCell('D' . $this->factures->count() + $totalIndex)
                    ->setValue('Total à payer');
                $sheet->getSheet()->getCell('E' . $this->factures->count() + $totalIndex)
                    ->setValue(sprintf(
                        '=SUM(%s:%s)',
                        'E2',
                        'E' . $this->factures->count() + 1
                    ));
            }
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            $this->colValues . '2:' . $this->colValues . $this->factures->count() + 2 =>  NumberFormat::FORMAT_CURRENCY_EUR,
            $this->colValues . '2:' . $this->colValues . $this->factures->count() + 3 =>  NumberFormat::FORMAT_CURRENCY_EUR,
        ];
    }
}

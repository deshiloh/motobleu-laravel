<?php

namespace App\Exports;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Enum\ReservationStatus;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\Reservation;
use app\Settings\BillSettings;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ReservationsExport implements WithStyles, WithCustomStartCell, WithHeadings, WithEvents, FromCollection, WithDrawings, WithMapping, WithColumnFormatting, WithColumnWidths, ShouldAutoSize
{
    private Collection $reservations;
    private int $indexDepart = 24;
    private int $startFooterIndex = 0;
    private string $lastColumn;
    private string $priceColumn;
    private string $coordinatePrices;
    private string $htTextCoordinate;
    private string $htValueCoordinate;
    private string $tvaTextCoordinate;
    private string $tvaValueCoordinate;
    private string $ttcTextCoordinate;
    private string $ttcValueCoordinate;
    private Entreprise $entreprise;
    private Carbon|false $datePeriod;
    private BillSettings $billSettings;

    public function __construct(int $year, int $month, Entreprise $entreprise)
    {
        $this->billSettings = app(BillSettings::class);
        $this->datePeriod = Carbon::create($year, $month, '1');

        $htTextColumn = in_array($entreprise->id, $this->billSettings->entreprise_without_command_field) ? 'H' : 'I';
        $tvaTextColumn = in_array($entreprise->id, $this->billSettings->entreprise_without_command_field) ? 'H' : 'I';
        $ttcTextColumn = in_array($entreprise->id, $this->billSettings->entreprise_without_command_field) ? 'H' : 'I';

        $this->priceColumn = in_array($entreprise->id, $this->billSettings->entreprise_without_command_field) ? 'I' : 'J';
        $this->lastColumn = in_array($entreprise->id, $this->billSettings->entreprise_without_command_field) ? 'K' : 'J';
        $this->entreprise = $entreprise;
        $this->reservations = $this->getReservations();

        if (in_array($this->entreprise->id, $this->billSettings->entreprises_cost_center_facturation)) {
            $this->lastColumn = 'K';
        }

        $this->coordinatePrices = sprintf(
            '%s%s:%s%s',
            $this->priceColumn,
            $this->indexDepart + 1,
            $this->priceColumn,
            $this->getReservations()->count() + $this->indexDepart
        );

        $this->generateHtCoordinates($htTextColumn);
        $this->generateTvaCoordinates($tvaTextColumn);
        $this->generateTtcCoordinates($ttcTextColumn);

        $this->startFooterIndex = $this->indexDepart + $this->reservations->count() + 7;
    }

    public function collection()
    {
        return $this->reservations;
    }

    public function styles(Worksheet $sheet): array
    {
        $styles = [];

        $styles[$this->indexDepart] = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => config('motobleu.export.backgroundColor')],
            ],
            'font' => [
                'color' => ['argb' => Color::COLOR_WHITE]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK]
                ]
            ]
        ];

        for ($i = 1; $i <= 7; $i++) {
            $index = sprintf('A%s:%s%s',
                $i,
                $this->lastColumn,
                $i
            );
            $styles[$index] = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => config('motobleu.export.backgroundColor')],
                ],
            ];
        }

        for ($j = $this->indexDepart + 1; $j <= $this->reservations->count() + $this->indexDepart; $j++) {
            if ($j % 2 == 0) {
                $styles[$j] = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE0E0E0'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK]
                        ]
                    ]
                ];
            } else {
                $styles[$j] = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK]
                        ]
                    ]
                ];
            }
        }

        // Footer styles
        for ($i = $this->startFooterIndex; $i <= $this->startFooterIndex + 5; $i ++) {
            $index = sprintf('A%s:%s%s',
                $i,
                $this->lastColumn,
                $i
            );
            $styles[$index] = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => config('motobleu.export.backgroundColor')],
                ],
                'font' => [
                    'color' => ['argb' => Color::COLOR_WHITE]
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
        }

        return $styles;
    }

    public function startCell(): string
    {
        return 'A' . $this->indexDepart;
    }

    private function getReservations(): Collection
    {
        $reservations = Reservation::whereMonth('pickup_date', $this->datePeriod->month)
            ->whereyear('pickup_date', $this->datePeriod->year)
            ->where('entreprise_id', $this->entreprise->id)
            ->where('encompte_pilote', '>', 0)
            ->whereIn('statut', [
                ReservationStatus::Confirmed->value,
                ReservationStatus::CanceledToPay->value,
                ReservationStatus::Billed
            ])
            ->orderBy('pickup_date')
            ->get();

        return $reservations;
    }

    public function headings(): array
    {
        if (in_array($this->entreprise->id, $this->billSettings->entreprise_without_command_field)) {
            $headers = [
                'Course N°',
                'Secrétaire',
                'Date',
                'Client',
                'Heure',
                'Départ',
                'Arrivée',
                'Commentaires',
                'Prix TTC (en €)',
            ];
        } else  {
            $headers = [
                'Course N°',
                'Code',
                'Secrétaire',
                'Date',
                'Client',
                'Heure',
                'Départ',
                'Arrivée',
                'Commentaires',
                'Prix TTC (en €)',
            ];
        }

        if (in_array($this->entreprise->id, $this->billSettings->entreprises_cost_center_facturation)) {
            array_push($headers, 'Facturation', 'COST CENTER');
        }

        return $headers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $sheet) {
                // Settings
                $sheet->getDelegate()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getDelegate()->getRowDimension(20)->setRowHeight(30);

                // Header
                $sheet->getSheet()->getCell($this->lastColumn . 9)->setValue(sprintf(
                    'Levallois, le %s',
                    Carbon::now()->format('d/m/Y')
                ))->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell($this->lastColumn . 10)->setValue(
                    $this->entreprise->nom
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $addressLocal = $this->entreprise
                    ->adresseEntreprises()
                    ->where('type', AdresseEntrepriseTypeEnum::PHYSIQUE->value)
                    ->first();

                if (!$addressLocal instanceof AdresseEntreprise) {
                    $addressLocal = $this->entreprise
                        ->adresseEntreprises()
                        ->where('type', AdresseEntrepriseTypeEnum::FACTURATION->value)
                        ->first();
                }

                $sheet->getSheet()->getCell($this->lastColumn . 11)->setValue(
                    $addressLocal->adresse_full
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('A14')->setValue(
                    'Période : ' . $this->datePeriod->monthName . ' ' . $this->datePeriod->year
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('A15')->setValue(
                    'Compte client : ' . $this->entreprise->nom
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('A16')->setValue(
                    'Contact facturation : ' . $this->entreprise
                        ->adresseEntreprises()
                        ->where('type', AdresseEntrepriseTypeEnum::FACTURATION->value)
                        ->first()->email
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $title = $sheet->getSheet()->getCell('G20')->setValue(
                    'RELEVE DE COURSES / PERIODE ' .$this->datePeriod->monthName . ' ' . $this->datePeriod->year
                );

                $title->getStyle()->getFont()->setBold(true);
                $title->getStyle()->getFont()->setSize(20);
                $title->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Affichage du total
                $sheet->getSheet()->getCell($this->htTextCoordinate)->setValue(
                    'Montant HT (en €)'
                );
                $htValueCelle = $sheet->getSheet()->getCell($this->htValueCoordinate);
                $htValueCelle->setValue(
                    sprintf(
                        '=%s/1.10',
                        $this->ttcValueCoordinate
                    )
                );
                $htValueCelle->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                $htValueCelle->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getSheet()->getCell($this->tvaTextCoordinate)->setValue(
                    'Montant de la TVA '
                );
                $tvaValueCell = $sheet->getSheet()->getCell($this->tvaValueCoordinate);
                $tvaValueCell->setValue(
                    sprintf('=%s*0.10', $this->htValueCoordinate)
                );
                $tvaValueCell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                $tvaValueCell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


                $sheet->getSheet()->getCell($this->ttcTextCoordinate)->setValue(
                    'Montant TTC (en €)'
                );
                $ttcValueCell = $sheet->getSheet()->getCell($this->ttcValueCoordinate);
                $ttcValueCell->setValue(
                    sprintf(
                        '=SUM(%s)',
                        $this->coordinatePrices
                    )
                );
                $ttcValueCell->getStyle()->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                $ttcValueCell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $paddingValues = [
                    strlen('MOTOBLEU') + 10, // Add 5 spaces for padding to second cell
                    strlen('26 - 28 rue Marius AUFAN 92300 LEVALLOIS PERRET') + 10, // Add 10 spaces for padding to third cell
                    strlen('SIRET : 82472195500014 - TVA intracommunautaire : FR69824721955') + 10, // Add 10 spaces for padding to fourth cell
                    strlen('Tél : +33 6 47 93 86 17 - contact@motobleu-paris.com') + 10// No additional padding needed for fifth cell
                ];

                // Footer values
                $sheet->getSheet()->getCell('A' . ($this->startFooterIndex + 1))->setValue(
                    str_pad('MOTOBLEU', $paddingValues[0], " ", STR_PAD_LEFT)
                );
                $sheet->getSheet()->getCell('A' . ($this->startFooterIndex + 2))->setValue(
                    str_pad('26 - 28 rue Marius AUFAN 92300 LEVALLOIS PERRET', $paddingValues[1], " ", STR_PAD_LEFT)
                );
                $sheet->getSheet()->getCell('A' . ($this->startFooterIndex + 3))->setValue(
                    str_pad('SIRET : 82472195500014 - TVA intracommunautaire : FR69824721955', $paddingValues[2], " ", STR_PAD_LEFT)
                );
                $sheet->getSheet()->getCell('A' . ($this->startFooterIndex + 4))->setValue(
                    str_pad('Tél : +33 6 47 93 86 17 - contact@motobleu-paris.com', $paddingValues[3], " ", STR_PAD_LEFT)
                );
            }
        ];
    }

    public function drawings(): array
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo motobleu');
        $drawing->setPath(public_path('storage/logo-pdf.png'));
        $drawing->setHeight(90);
        $drawing->setOffsetX(20);
        $drawing->setCoordinates('A2');

        return [$drawing];
    }

    /**
     * @param Reservation $row
     * @return array
     */
    public function map($row): array
    {
        if (in_array($this->entreprise->id, $this->billSettings->entreprise_without_command_field)) {
            $datas = [
                $row->reference,
                $row->passager->user->full_name,
                $row->pickup_date->format('d/m/Y'),
                $row->passager->nom,
                $row->pickup_date->format('H:i'),
                $row->display_from,
                $row->display_to,
                $row->comment_facture,
                $row->total_ttc,
            ];
        } else {
            $datas = [
                $row->reference,
                $row->commande,
                $row->passager->user->full_name,
                $row->pickup_date->format('d/m/Y'),
                $row->passager->nom,
                $row->pickup_date->format('H:i'),
                $row->display_from,
                $row->display_to,
                $row->comment_facture,
                $row->total_ttc,
            ];
        }

        if (in_array($this->entreprise->id, $this->billSettings->entreprises_cost_center_facturation)) {
            array_push($datas, $row->passager->typeFacturation->nom ?? 'NC', $row->passager->costCenter->nom ?? 'NC');
        }

        return $datas;
    }

    public function columnFormats(): array
    {
        return [
            $this->coordinatePrices => NumberFormat::FORMAT_NUMBER_00
        ];
    }

    /**
     * @param $textColumn
     * @return void
     */
    private function generateHtCoordinates($textColumn): void
    {
        $position = $this->indexDepart + $this->getReservations()->count() + 2;

        $this->htTextCoordinate = sprintf(
            '%s%s',
            $textColumn,
            $position,
        );

        $this->htValueCoordinate = sprintf(
            '%s%s',
            $this->priceColumn,
            $position,
        );
    }

    /**
     * @param $textColumn
     * @return void
     */
    private function generateTvaCoordinates($textColumn): void
    {
        $position = $this->indexDepart + $this->getReservations()->count() + 3;

        $this->tvaTextCoordinate = sprintf(
            '%s%s',
            $textColumn,
            $position,
        );

        $this->tvaValueCoordinate = sprintf(
            '%s%s',
            $this->priceColumn,
            $position,
        );
    }

    /**
     * @param $textColumn
     * @return void
     */
    private function generateTtcCoordinates($textColumn): void
    {
        $position = $this->indexDepart + $this->getReservations()->count() + 4;

        $this->ttcTextCoordinate = sprintf(
            '%s%s',
            $textColumn,
            $position,
        );

        $this->ttcValueCoordinate = sprintf(
            '%s%s',
            $this->priceColumn,
            $position,
        );
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'G' => 40,
        ];
    }
}

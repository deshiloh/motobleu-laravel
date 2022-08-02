<?php

namespace App\Exports;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Models\Entreprise;
use App\Models\Reservation;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
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
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ReservationsExport implements WithStyles, ShouldAutoSize, WithDefaultStyles, WithCustomStartCell, WithHeadings, WithEvents, FromCollection, WithDrawings, WithMapping, WithColumnFormatting
{
    private \Illuminate\Database\Eloquent\Collection $reservations;
    private string $entrepriseName;

    private int $indexDepart = 23;
    private string $lastColumn;
    private string $priceColumn;
    private string $coordinatePrices;
    private string $htTextCoordinate;
    private string $htValueCoordinate;
    private string $tvaTextCoordinate;
    private string $tvaValueCoordinate;
    private string $ttcTextCoordinate;
    private string $ttcValueCoordinate;
    private int $year;
    private int $month;
    private Entreprise $entreprise;
    private $datePeriod;

    public function __construct(int $year, int $month, Entreprise $entreprise)
    {
        $htTextColumn = 'H';
        $tvaTextColumn = 'H';
        $ttcTextColumn = 'H';

        $this->entrepriseName = $entreprise->nom;
        $this->reservations = $this->getReservations();

        $this->priceColumn = 'I';
        $this->lastColumn = 'I';

        if (in_array($this->entrepriseName, config('motobleu.export.entreprisesCode'))) {
            $this->priceColumn = 'J';
            $this->lastColumn = 'J';

            $htTextColumn = 'I';
            $tvaTextColumn = 'I';
            $ttcTextColumn = 'I';
        }

        if (in_array($this->entrepriseName, config('motobleu.export.entreprisesFacturation'))) {
            $this->lastColumn = 'K';
        }

        $this->coordinatePrices = sprintf(
            '%s%s:%s%s',
            $this->priceColumn,
            $this->indexDepart + 1,
            $this->priceColumn,
            $this->getReservations()->count() - 1
        );

        $this->generateHtCoordinates($htTextColumn);
        $this->generateTvaCoordinates($tvaTextColumn);
        $this->generateTtcCoordinates($ttcTextColumn);

        $this->year = $year;
        $this->month = $month;
        $this->entreprise = $entreprise;

        $this->datePeriod = Carbon::create($year, $month, '1');
    }

    public function collection()
    {
        return $this->reservations;
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
    }

    public function styles(Worksheet $sheet)
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

        for ($i = $this->indexDepart; $i <= $this->reservations->count() + $this->indexDepart; $i++) {
            if ($i % 2 == 0) {
                $styles[$i] = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE0E0E0'],
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
        return $styles;
    }

    public function startCell(): string
    {
        return 'A' . $this->indexDepart;
    }

    private function getReservations()
    {
        return Reservation::all();
    }

    public function headings(): array
    {
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

        if (in_array($this->entrepriseName, config('motobleu.export.entreprisesCode'))) {
            array_splice($headers, 1, 0, [
                'Code'
            ]);
        }

        if (in_array($this->entrepriseName, config('motobleu.export.entreprisesFacturation'))) {
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
                $sheet->getSheet()->getProtection()->setSheet(true);
                $sheet->getDelegate()->getRowDimension(20)->setRowHeight(30);

                // Header
                $sheet->getSheet()->getCell($this->lastColumn . 9)->setValue(sprintf(
                    'Levallois, le %s',
                    Carbon::now()->format('d/m/Y')
                ))->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell($this->lastColumn . 10)->setValue(
                    $this->entreprise->nom
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell($this->lastColumn . 11)->setValue(
                    $this->entreprise->getAdresse(AdresseEntrepriseTypeEnum::PHYSIQUE)->adresse_full
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('A14')->setValue(
                    'Période :'
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('B14')->setValue(
                    $this->datePeriod->monthName . ' ' . $this->datePeriod->year
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('A15')->setValue(
                    'Compte client :'
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('B15')->setValue(
                    $this->entreprise->nom
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('A16')->setValue(
                    'Contact facturation:'
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getSheet()->getCell('B16')->setValue(
                    $this->entreprise->getAdresse(AdresseEntrepriseTypeEnum::FACTURATION)->email
                )->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $title = $sheet->getSheet()->getCell('G20')->setValue(
                    'RELEVE DE COURSES / PERIODE ' .$this->datePeriod->monthName . ' ' . $this->datePeriod->year
                );

                $title->getStyle()->getFont()->setBold(true);
                $title->getStyle()->getFont()->setSize(20);

                // Affichage du total
                $sheet->getSheet()->getCell($this->htTextCoordinate)->setValue(
                    'Montant HT (en €)'
                );
                $sheet->getSheet()->getCell($this->htValueCoordinate)->setValue(
                    sprintf(
                        '=%s/1.10',
                        $this->ttcValueCoordinate
                    )
                );
                $sheet->getSheet()->getCell($this->tvaTextCoordinate)->setValue(
                    'Montant de la TVA '
                );
                $sheet->getSheet()->getCell($this->tvaValueCoordinate)->setValue(
                    sprintf('=%s*0.10', $this->htValueCoordinate)
                );
                $sheet->getSheet()->getCell($this->ttcTextCoordinate)->setValue(
                    'Montant TTC (en €)'
                );
                $sheet->getSheet()->getCell($this->ttcValueCoordinate)->setValue(
                    sprintf(
                        '=SUM(%s)',
                        $this->coordinatePrices
                    )
                );
            }
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo motobleu');
        $drawing->setPath(public_path('storage/Motobleu.png'));
        $drawing->setHeight(90);
        $drawing->setOffsetX(20);
        $drawing->setCoordinates('A2');

        return $drawing;
    }

    /**
     * @param Reservation $row
     * @return array
     */
    public function map($row): array
    {
        $datas = [
            $row->reference,
            $row->passager->user->full_name,
            $row->pickup_date->format('d/m/Y'),
            $row->passager->nom,
            $row->pickup_date->format('H:s'),
            $row->display_from,
            $row->display_to,
            $row->comment,
            $row->tarif,
        ];

        if (in_array($this->entrepriseName, config('motobleu.export.entreprisesCode'))) {
            array_splice($datas, 1, 0, [
                'CODE'
            ]);
        }

        if (in_array($this->entrepriseName, config('motobleu.export.entreprisesFacturation'))) {
            array_push($datas, 'test', 'trest');
        }

        return $datas;
    }

    public function columnFormats(): array
    {
        return [
            $this->coordinatePrices => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
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
}

<?php

namespace App\Exports;

use App\Enum\ReservationStatus;
use App\Models\Pilote;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReservationPiloteExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents, WithColumnFormatting, WithColumnWidths
{
    private array $period;
    private Pilote $pilote;
    private int $indexDepart;

    private string $lastColumn;

    /**
     * @var Builder[]|Collection
     */
    private array|Collection $reservations;

    public function __construct(array $period, Pilote $pilote)
    {
        $this->period = $period;
        $this->pilote = $pilote;

        $this->indexDepart = 13;

        $this->lastColumn = 'I';

        ray()->showQueries();
        $this->reservations = Reservation::where('pilote_id', $pilote->id)
            ->whereIn('statut', [ReservationStatus::Confirmed->value, ReservationStatus::Billed])
            ->whereDate('pickup_date', '>=', $this->period[0]->format('Y-m-d'))
            ->whereDate('pickup_date', '<=', $this->period[1]->format('Y-m-d'))
            ->orderBy('pickup_date')
            ->get();
    }

    public function collection()
    {
        return $this->reservations;
    }

    /**
     * @param Reservation $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->pickup_date->format('d/m/Y'),
            $row->pickup_date->format('H:i'),
            $row->passager->nom,
            $row->display_from,
            $row->display_to,
            $row->comment_pilote,
            $row->encaisse_pilote,
            $row->encompte_pilote,
            $row->reference
        ];
    }

    public function headings(): array
    {
        return [
            'Date',
            'Heure',
            'Passager',
            'Départ',
            'Arrivée',
            'Commentaire',
            'Encaisse',
            'Encompte',
            'Course N°'
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $styles = [];

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

        // Header Data Style
        $styles[$this->indexDepart] = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => config('motobleu.export.backgroundColor')],
            ],
            'font' => [
                'color' => ['argb' => Color::COLOR_WHITE],
                'size' => 14
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
            ],

        ];

        // Styles de la liste réservations
        for ($i = $this->indexDepart + 1; $i <= $this->reservations->count() + $this->indexDepart; $i++) {
            $styles[$i] = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK]
                    ]
                ],
                'font' => [
                    'size' => 14
                ]
            ];

            if ($i % 2 == 0) {
                $styles[$i] = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE0E0E0'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK]
                        ]
                    ],
                    'font' => [
                        'size' => 14
                    ]
                ];
            }
        }

        foreach (['B', 'D', 'F', 'H'] as $column) {
            $styles[$column . $this->indexDepart + $this->reservations->count() + 2] = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => config('motobleu.export.backgroundColor')],
                ]
            ];
        }

        foreach (['B', 'D', 'F', 'H'] as $column) {
            $styles[$column . $this->indexDepart + $this->reservations->count() + 3] = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => config('motobleu.export.backgroundColor')],
                ]
            ];
        }

        $start = $this->indexDepart + $this->reservations->count() + 6;
        for ($i = $start; $i <= $start + 4; $i++) {
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

        return $styles;
    }

    public function startCell(): string
    {
        return 'A' . $this->indexDepart;
    }

    public function columnFormats(): array
    {
        return [
            $this->encompteColumns() => NumberFormat::FORMAT_CURRENCY_EUR,
            $this->encaisseColumns() => NumberFormat::FORMAT_CURRENCY_EUR,
        ];
    }

    public function registerEvents(): array
    {
        return [
            Sheet::class => function(Sheet $sheet) {
                $sheet->getStyle('A13:J13')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
            },

            AfterSheet::class => function(AfterSheet $sheet) {
                // Settings
                $sheet->getDelegate()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                $sheet->getDelegate()->getStyle("D")->getAlignment()->setWrapText(true);
                $sheet->getDelegate()->getStyle("E")->getAlignment()->setWrapText(true);

                for ($i = $this->indexDepart + 1; $i <= $this->reservations->count() + $this->indexDepart; $i ++) {
                    $currentEncaisseCell = $sheet->getSheet()->getCell('G' . $i);
                    $currentEncaisseValue = $sheet->getSheet()->getCell('G' . $i)->getValue();

                    if (is_null($currentEncaisseValue)) {
                        $currentEncaisseCell->setValue(0);
                    }

                    $currentEncompteCell = $currentEncaisseCell = $sheet->getSheet()->getCell('H' . $i);
                    $currentEncompteValue = $sheet->getSheet()->getCell('H' . $i)->getValue();

                    if (is_null($currentEncompteValue)) {
                        $currentEncompteCell->setValue(0);
                    }
                }

                // Header
                $sheet->getSheet()->getCell('A' . 9)
                    ->setValue('Tableau recap courses');

                $titleStyles = $sheet->getSheet()->getCell('A' . 9)->getStyle();
                //$titleStyles->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $titleStyles->getFont()->setSize(14);
                $titleStyles->getFont()->setBold(true);

                /** @var Carbon $currentDate */
                $currentDate = $this->period[1];
                $sheet->getSheet()->getCell('A' . 10)
                    ->setValue(
                        sprintf(
                            '%s / %s / %s',
                            $this->pilote->full_name,
                            $this->pilote->email,
                            $currentDate->isoFormat('MMMM') . ' ' . $currentDate->format('Y')
                        )
                    );

                $titleStyles = $sheet->getSheet()->getCell('A' . 10)->getStyle();
                //$titleStyles->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $titleStyles->getFont()->setSize(14);
                $titleStyles->getFont()->setBold(true);


                // CA Cell
                $index = $this->indexDepart + $this->reservations->count() + 2;
                $CaLabelle = $sheet->getSheet()->getCell('A' . $index);
                $CaLabelle->setValue(
                    'Chiffre d\'affaire'
                );
                $CaLabelle->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $CaLabelle->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('000000');
                $CaLabelle->getStyle()->getFont()->setSize(14);
                $caValueCell = $sheet->getSheet()->getCell('A' . $index + 1);
                $caValueCell->setValue(
                    sprintf(
                        '=SUM(%s)',
                        'E' . $index + 1 . ':G' . $index + 1
                    )
                );
                $caValueCell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $caValueCell->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR);
                $caValueCell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('000000');
                $caValueCell->getStyle()->getFont()->setSize(14);

                // COM Cell
                $comCell = 'C' . $index + 1;
                $comLabelle = $sheet->getSheet()->getCell('C' . $index);
                $comLabelle->setValue(
                    'COM 15%'
                );
                $comLabelle->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $comLabelle->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('000000');
                $comLabelle->getStyle()->getFont()->setSize(14);
                $comValue = $sheet->getSheet()->getCell('C' . $index + 1);
                $comValue->setValue(
                    '=(A'. $index + 1 .') * 0.15'
                );
                $comValue->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $comValue->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('000000');
                $comValue->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR);
                $comValue->getStyle()->getFont()->setSize(14);

                // Encaisse Cell
                $enCaisseLabelle = $sheet->getSheet()->getCell('E' . $index);
                $enCaisseLabelle->setValue(
                    'ENCAISSE'
                );
                $enCaisseLabelle->getStyle()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $enCaisseLabelle->getStyle()->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
                $enCaisseLabelle->getStyle()->getFont()->setSize(14);
                $encaisseValue = $sheet->getSheet()->getCell('E' . $index + 1);
                $encaisseValue->setValue(
                    sprintf('=SUM(%s)', $this->encaisseColumns())
                );
                $encaisseValue->getStyle()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $encaisseValue->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR);
                $encaisseValue->getStyle()->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
                $encaisseValue->getStyle()->getFont()->setSize(14);

                // Encompte Cell
                $encompteCell = 'G' . $index + 1;
                $enCompteLabelle = $sheet->getSheet()->getCell('G' . $index);
                $enCompteLabelle->setValue(
                    'EN COMPTE'
                );
                $enCompteLabelle->getStyle()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $enCompteLabelle->getStyle()->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
                $enCompteLabelle->getStyle()->getFont()->setSize(14);
                $encompteValue = $sheet->getSheet()->getCell('G' . $index + 1);
                $encompteValue->setValue(
                    sprintf('=SUM(%s)', $this->encompteColumns())
                );
                $encompteValue->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR);
                $encompteValue->getStyle()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $encompteValue->getStyle()->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
                $encompteValue->getStyle()->getFont()->setSize(14);

                // Total Cell
                $totalLabel = $sheet->getSheet()->getCell('I' . $index);
                $totalLabel->setValue(
                    'TOTAL'
                );
                $totalLabel->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $totalLabel->getStyle()->getFont()->getColor()->setRGB(Color::COLOR_RED);
                $totalLabel->getStyle()->getFont()->setBold(true);
                $totalLabel->getStyle()->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('000000');
                $totalLabel->getStyle()->getFont()->setSize(14);
                $totalValue = $sheet->getSheet()->getCell('I' . $index + 1);
                $totalValue->setValue(
                    sprintf('=(%s-%s)',
                        $encompteCell,
                        $comCell
                    )
                );
                $style = $totalValue->getStyle();
                $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $style->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR);
                $style->getFont()->getColor()->setRGB(Color::COLOR_RED);
                $style->getFont()->setBold(true);
                $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('000000');
                $style->getFont()->setSize(14);

                // FOOTER
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo motobleu');
                $drawing->setPath(public_path('storage/logo-pdf.png'));
                $drawing->setHeight(90);
                $drawing->setOffsetX(20);
                $drawing->setCoordinates('A2');
                $drawing->setWorksheet($sheet->getSheet()->getDelegate());

                $drawing = new Drawing();
                $drawing->setName('LogoFooter');
                $drawing->setDescription('Logo motobleu');
                $drawing->setPath(public_path('storage/textfooter.png'));
                $drawing->setHeight(60);
                $drawing->setOffsetX(20);
                $drawing->setCoordinates('A' . $this->indexDepart + $this->reservations->count() + 7);
                $drawing->setWorksheet($sheet->getSheet()->getDelegate());
            }
        ];
    }

    private function encompteColumns(): string
    {
        return sprintf('H%s:H%s',
            $this->indexDepart,
            $this->indexDepart + $this->reservations->count()
        );
    }

    private function encaisseColumns(): string
    {
        return sprintf('G%s:G%s',
            $this->indexDepart,
            $this->indexDepart + $this->reservations->count()
        );
    }

    public function footer(): array
    {
        return [
            [
                'image' => public_path('storage/motobleu.png')
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'D' => 70,
            'E' => 70
        ];
    }
}

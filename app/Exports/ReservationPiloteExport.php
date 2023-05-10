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
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
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
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReservationPiloteExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents, WithColumnFormatting
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

        $this->reservations = Reservation::query()
            ->where('pilote_id', $pilote->id)
            ->where('statut', ReservationStatus::Confirmed->value)
            ->whereBetween('pickup_date', $period)
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
        $tarifs = sprintf('F%s:F%s', $this->indexDepart, $this->indexDepart + $this->reservations->count());
        return [
            $tarifs => NumberFormat::FORMAT_CURRENCY_EUR,
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

                // Header
                $sheet->getSheet()->getCell('D' . 9)
                    ->setValue('Tableau recap courses');

                $titleStyles = $sheet->getSheet()->getCell('D' . 9)->getStyle();
                $titleStyles->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $titleStyles->getFont()->setSize(14);
                $titleStyles->getFont()->setBold(true);

                /** @var Carbon $currentDate */
                $currentDate = $this->period[1];
                $sheet->getSheet()->getCell('D' . 10)
                    ->setValue(
                        sprintf(
                            '%s / %s / %s',
                            $this->pilote->full_name,
                            $this->pilote->email,
                            $currentDate->isoFormat('MMMM') . ' ' . $currentDate->format('Y')
                        )
                    );

                $titleStyles = $sheet->getSheet()->getCell('D' . 10)->getStyle();
                $titleStyles->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $titleStyles->getFont()->setSize(14);
                $titleStyles->getFont()->setBold(true);

                // Total Encaisse
                $index = $this->indexDepart + $this->reservations->count() + 1;
                $sheet->getSheet()->getCell('G' . $index)->setValue(
                    sprintf(
                        '=SUM(%s)',
                        $this->encaisseColumns()
                    )
                );

                // Total encompte
                $sheet->getSheet()->getCell('H' . $index)->setValue(
                    sprintf(
                        '=SUM(%s)',
                        $this->encompteColumns()
                    )
                );

                // CA Cell
                $index = $index + 1;
                $CaLabelle = $sheet->getSheet()->getCell('A' . $index);
                $CaLabelle->setValue(
                    'Chiffre d\'affaire'
                );
                $CaLabelle->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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

                // COM Cell
                $comCell = 'C' . $index + 1;
                $comLabelle = $sheet->getSheet()->getCell('C' . $index);
                $comLabelle->setValue(
                    'COM 15%'
                );
                $comLabelle->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $comValue = $sheet->getSheet()->getCell('C' . $index + 1);
                $comValue->setValue(
                    '=(A'. $index + 1 .') * 0.15'
                );
                $comValue->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Encaisse Cell
                $enCaisseLabelle = $sheet->getSheet()->getCell('E' . $index);
                $enCaisseLabelle->setValue(
                    'ENCAISSE'
                );
                $enCaisseLabelle->getStyle()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $encaisseValue = $sheet->getSheet()->getCell('E' . $index + 1);
                $encaisseValue->setValue(
                    sprintf('=SUM(%s)', $this->encaisseColumns())
                );
                $encaisseValue->getStyle()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $encaisseValue->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR);

                // Encompte Cell
                $encompteCell = 'G' . $index + 1;
                $enCompteLabelle = $sheet->getSheet()->getCell('G' . $index);
                $enCompteLabelle->setValue(
                    'EN COMPTE'
                );
                $enCompteLabelle->getStyle()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $encompteValue = $sheet->getSheet()->getCell('G' . $index + 1);
                $encompteValue->setValue(
                    sprintf('=SUM(%s)', $this->encompteColumns())
                );
                $encompteValue->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR);
                $encompteValue->getStyle()->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Total Cell
                $totalLabel = $sheet->getSheet()->getCell('I' . $index);
                $totalLabel->setValue(
                    'TOTAL'
                );
                $totalLabel->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $totalLabel->getStyle()->getFont()->getColor()->setRGB(Color::COLOR_RED);
                $totalLabel->getStyle()->getFont()->setBold(true);
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
                $drawing->setName('Logo');
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
}

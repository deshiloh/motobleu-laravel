<?php

namespace App\Exports;

use App\Enum\ReservationStatus;
use App\Models\Pilote;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReservationPiloteExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents, WithDefaultStyles, WithColumnFormatting
{
    private array $period;
    private Pilote $pilote;
    private int $indexDepart;

    private string $lastColumn;

    /**
     * @var Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private array|\Illuminate\Database\Eloquent\Collection $reservations;

    public function __construct(array $period, Pilote $pilote)
    {
        $this->period = $period;
        $this->pilote = $pilote;

        $this->indexDepart = 13;

        $this->lastColumn = 'K';

        $this->reservations = Reservation::query()
            ->where('pilote_id', $pilote->id)
            ->where('statut', ReservationStatus::Confirmed->value)
            ->whereBetween('pickup_date', $period)
            ->orderBy('pickup_date', 'desc')
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
            $row->tarif_pilote,
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
            'Tarif',
            'Commentaire',
            'Encaisse',
            'Encompte',
            'Course N°'
        ];
    }

    public function styles(Worksheet $sheet)
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
        for ($i = $this->indexDepart; $i <= $this->reservations->count() + $this->indexDepart; $i++) {
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

        foreach (['B', 'D', 'F', 'H', 'J', 'K'] as $column) {
            $styles[$column . $this->indexDepart + $this->reservations->count() + 2] = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => config('motobleu.export.backgroundColor')],
                ]
            ];
        }

        foreach (['B', 'D', 'F', 'H', 'J', 'K'] as $column) {
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
                'K',
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
            $tarifs => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            $this->majorationColumns() => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            $this->encompteColumns() => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            $this->encaisseColumns() => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $sheet) {
                // Settings
                $sheet->getDelegate()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                // Header
                $sheet->getSheet()->getCell('E' . 9)
                    ->setValue('Tableau recap courses');

                $titleStyles = $sheet->getSheet()->getCell('E' . 9)->getStyle();
                $titleStyles->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $titleStyles->getFont()->setSize(14);
                $titleStyles->getFont()->setBold(true);

                /** @var Carbon $currentDate */
                $currentDate = $this->period[1];
                $sheet->getSheet()->getCell('E' . 10)
                    ->setValue(
                        sprintf(
                            '%s / %s / %s',
                            $this->pilote->full_name,
                            $this->pilote->email,
                            $currentDate->isoFormat('MMMM') . ' ' . $currentDate->format('Y')
                        )
                    );

                $titleStyles = $sheet->getSheet()->getCell('E' . 10)->getStyle();
                $titleStyles->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $titleStyles->getFont()->setSize(14);
                $titleStyles->getFont()->setBold(true);

                // Total Encaisse
                $index = $this->indexDepart + $this->reservations->count() + 1;
                $sheet->getSheet()->getCell('I' . $index)->setValue(
                    sprintf(
                        '=SUM(%s)',
                        $this->encompteColumns()
                    )
                );

                // Total encompte
                $sheet->getSheet()->getCell('J' . $index)->setValue(
                    sprintf(
                        '=SUM(%s)',
                        $this->encaisseColumns()
                    )
                );

                // CA Cell
                $index = $index + 1;
                $sheet->getSheet()->getCell('A' . $index)->setValue(
                    'Chiffre d\'affaire'
                );
                $caValueCell = $sheet->getSheet()->getCell('A' . $index + 1);
                $caValueCell->setValue(
                    sprintf(
                        '=SUM(%s)',
                        'E' . $index + 1 . ':G' . $index + 1
                    )
                );
                $caValueCell->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

                // COM Cell
                $comCell = 'C' . $index + 1;
                $sheet->getSheet()->getCell('C' . $index)->setValue(
                    'COM 15%'
                );
                $sheet->getSheet()->getCell('C' . $index + 1)->setValue(
                    '=(A'. $index + 1 .') * 0.15'
                );

                // Encaisse Cell
                $sheet->getSheet()->getCell('E' . $index)->setValue(
                    'ENCAISSE'
                );
                $encaisseValue = $sheet->getSheet()->getCell('E' . $index + 1);
                $encaisseValue->setValue(
                    sprintf('=SUM(%s)', $this->encaisseColumns())
                );
                $encaisseValue->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

                // Encompte Cell
                $encompteCell = 'G' . $index + 1;
                $sheet->getSheet()->getCell('G' . $index)->setValue(
                    'EN COMPTE'
                );
                $encompteValue = $sheet->getSheet()->getCell('G' . $index + 1);
                $encompteValue->setValue(
                    sprintf('=SUM(%s)', $this->encompteColumns())
                );
                $encompteValue->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

                // Total Cell
                $sheet->getSheet()->getCell('I' . $index)->setValue(
                    'TOTAL'
                );
                $totalValue = $sheet->getSheet()->getCell('I' . $index + 1);
                $totalValue->setValue(
                    sprintf('=(%s-%s)',
                        $encompteCell,
                        $comCell
                    )
                );
                $totalValue->getStyle()
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

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

    public function defaultStyles(Style $defaultStyle): array
    {
        return [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
    }

    private function majorationColumns(): string
    {
        return sprintf('H%s:H%s',
            $this->indexDepart,
            $this->indexDepart + $this->reservations->count()
        );
    }

    private function encompteColumns(): string
    {
        return sprintf('I%s:I%s',
            $this->indexDepart,
            $this->indexDepart + $this->reservations->count()
        );
    }

    private function encaisseColumns(): string
    {
        return sprintf('J%s:J%s',
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

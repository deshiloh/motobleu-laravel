<?php

namespace App\Exports;

use App\Models\Reservation;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
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
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ReservationsExport implements WithStyles, ShouldAutoSize, WithDefaultStyles, WithCustomStartCell, WithHeadings, WithEvents, FromCollection, WithDrawings, WithMapping, WithColumnFormatting
{
    private \Illuminate\Database\Eloquent\Collection $reservations;
    private $indexDepart = 23;
    private $columnEnd = 'I';
    private $entrepriseName;

    public function __construct()
    {
        $this->entrepriseName = 'toto';
        $this->reservations = $this->getReservations();
        $this->calculEndColumn();
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
                'startColor' => ['argb' => 'FF09158D'],
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
                $this->columnEnd,
                $i
            );
            $styles[$index] = [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF09158D'],
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

        if (in_array($this->entrepriseName, config('motobleu.export.entreprise'))){
            array_push($headers, 'Facturation', 'COST CENTER');
        }

        return $headers;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $sheet) {

                $sheet->getSheet()->getCell('A14')->setValue('test');

                // Affichage du total
                $indexHT = $this->reservations->count() + $this->indexDepart + 1;
                $indexTTC = $this->reservations->count() + $this->indexDepart + 3;
                $countStart = $this->indexDepart + 1;
                $countStop = $countStart + $this->reservations->count() - 1;

                $sheet->getSheet()->getCell('H' . $indexHT)->setValue(
                    'Montant HT (en €)'
                );
                $sheet->getSheet()->getCell('I' . $indexHT)->setValue(
                    sprintf(
                        '=I%s/1.10',
                        $indexTTC
                    )
                );
                $sheet->getSheet()->getCell('H' . $indexHT + 1)->setValue(
                    'Montant de la TVA '
                );
                $sheet->getSheet()->getCell('I' . $indexHT + 1)->setValue(
                    sprintf('=I%s*0.10', $indexHT)
                );
                $sheet->getSheet()->getCell('H' . $indexTTC)->setValue(
                    'Montant TTC (en €)'
                );
                $sheet->getSheet()->getCell('I' . $indexTTC)->setValue(
                    sprintf(
                        '=SUM(I%s:I%s)',
                        $countStart,
                        $countStop
                    )
                );
            }
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('storage/Motobleu.png'));
        $drawing->setHeight(90);
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

        if (in_array($this->entrepriseName, config('motobleu.export.entreprise'))) {
            array_push($datas, 'test', 'trest');
        }

        return $datas;
    }

    public function columnFormats(): array
    {
        return [
            'I24:I64' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
        ];
    }

    /**
     * @return void
     */
    private function calculEndColumn(): void
    {
        if (in_array($this->entrepriseName, config('motobleu.export.entreprise'))) {
            $this->columnEnd = 'K';
        }
    }
}

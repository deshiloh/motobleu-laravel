<?php

namespace App\Exports;

use App\Models\Reservation;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ReservationsExport implements WithStyles, ShouldAutoSize, WithDefaultStyles, WithCustomStartCell, WithHeadings, WithEvents, FromCollection, WithDrawings
{
    private \Illuminate\Database\Eloquent\Collection $reservations;

    public function __construct()
    {
        $this->reservations = $this->getReservations();
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

        $styles = [
            'A1:E1' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF09158D'],
                ],
                'alignment' => [
                    'indent' => 1,
                ],
            ],
            'A2:E2' => [
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
            ],
        ];

        for ($i = 3; $i <= $this->reservations->count() + 2; $i++) {
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
        return 'A7';
    }

    private function getReservations()
    {
        return Reservation::all('reference', 'pickup_date');
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Date'
        ];
    }

    /*public function view(): \Illuminate\Contracts\View\View
    {
        return view('exports.reservations', [
            'reservations' => $this->reservations
        ]);
    }*/

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $sheet) {
                $sheet->sheet->getDelegate()->getRowDimension(1)->setRowHeight(80);
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
        $drawing->setCoordinates('A1');

        return $drawing;
    }
}

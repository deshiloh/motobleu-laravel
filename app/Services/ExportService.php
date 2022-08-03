<?php

namespace App\Services;

use App\Exports\ReservationsExport;
use App\Models\Entreprise;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    public function exportReservations(int $year, int $month, Entreprise $entreprise)
    {
        if (in_array($entreprise->nom, config('motobleu.export.entrepriseEnableForXlsExport'))) {
            // Export en XLS
            return Excel::download(new ReservationsExport($year, $month, $entreprise), 'reservations.xlsx');
        } else {
            // Export PDF
            $pdf = Pdf::loadView('pdf.export.reservations');
            return $pdf->download('reservations.pdf');
        }
    }
}

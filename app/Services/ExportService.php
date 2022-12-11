<?php

namespace App\Services;

use App\Exports\ReservationsExport;
use App\Models\Entreprise;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportReservations(int $year, int $month, Entreprise $entreprise): Response|BinaryFileResponse
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

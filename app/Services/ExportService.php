<?php

namespace App\Services;

use App\Exports\ReservationPiloteExport;
use App\Exports\ReservationsExport;
use App\Models\Entreprise;
use App\Models\Pilote;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportForFacturation(int $year, int $month, Entreprise $entreprise): Response|BinaryFileResponse
    {
        if (in_array($entreprise->nom, config('motobleu.export.entrepriseEnableForXlsExport'))) {
            return Excel::download(new ReservationsExport($year, $month, $entreprise), 'reservations.xlsx');
        } else {
            $pdf = Pdf::loadView('exports.reservations.pdf-facture', [
                'entreprise' => $entreprise,
                'year' => $year,
                'month' => $month
            ]);

            return $pdf->download('reservations.pdf');
        }
    }

    /**
     * Exportation des réservations au format Excel pour un pilote et une période données.
     * @param array $period
     * @param Pilote $pilote
     * @return BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportForPilote(array $period, Pilote $pilote): BinaryFileResponse
    {
        $name = sprintf('Moto Bleu-%s-%s.xlsx', $period[1]->format('m'), $period[1]->format("Y"));
        return Excel::download(new ReservationPiloteExport($period, $pilote), $name);
    }
}

<?php

namespace App\Services;

use App\Exports\ReservationPiloteExport;
use App\Exports\ReservationsExport;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Pilote;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
     * Exporte des factures selon une période et une entreprise sélectionnée
     * @param string|null $dateDebut
     * @param string|null $dateFin
     * @param int|null $entreprise
     * @return \Barryvdh\DomPDF\PDF
     */
    public function exportFactures(?string $dateDebut, ?string $dateFin, ?int $entreprise): \Barryvdh\DomPDF\PDF
    {
        $dateDebut = Carbon::createFromFormat("Y-m-d", $dateDebut);
        $dateFin = Carbon::createFromFormat("Y-m-d", $dateFin);

        $factures = Facture::orderBy('id', 'DESC')
            ->when($entreprise, function(Builder $query) use ($entreprise) {
                return $query->whereHas('reservations', function (Builder $query) use ($entreprise) {
                    return $query->where('entreprise_id', $entreprise);
                });
            })
            ->when($dateDebut && $dateFin, function(Builder $query) use ($dateDebut, $dateFin) {
                return $query
                    ->whereIn('year', [$dateDebut->year, $dateFin->year])
                    ->whereIn('month', [$dateDebut->month, $dateFin->month]);
            })->get();

        return Pdf::loadView('exports.factures.pdf-export', [
            'entreprise' => $entreprise ? Entreprise::find($entreprise) : false,
            'debut' => $dateDebut,
            'fin' => $dateFin,
            'factures' => $factures
        ]);
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
        $name = sprintf(\Str::slug($pilote->full_name) . '-%s-%s.xlsx', $period[1]->format('m'), $period[1]->format("Y"));
        return Excel::download(new ReservationPiloteExport($period, $pilote), $name);
    }
}

<?php

namespace App\Services;

use App\Enum\BillStatut;
use App\Enum\ReservationStatus;
use App\Exports\FactureExport;
use App\Exports\ReservationPiloteExport;
use App\Models\Entreprise;
use App\Models\Facture;
use App\Models\Pilote;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
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
        $months = [];
        $years = [];

        for ($i = $dateDebut->month; $i <= $dateFin->month; $i ++) {
            $months[] = $i;
        }

        for ($currentYear = $dateDebut->year; $currentYear <= $dateFin->year; $currentYear ++) {
            $years[] = $currentYear;
        }

        $factures = Facture::orderBy('id')
            ->whereIn('statut', [BillStatut::COMPLETED, BillStatut::CANCEL])
            ->when($entreprise, function(Builder $query) use ($entreprise) {
                return $query->whereHas('reservations', function (Builder $query) use ($entreprise) {
                    return $query->where('entreprise_id', $entreprise);
                });
            })
            ->when($dateDebut && $dateFin, function(Builder $query) use ($dateDebut, $dateFin, $months, $years) {
                return $query
                    ->whereIn('year', $years)
                    ->whereIn('month', $months);
            })->get();

        return Pdf::loadView('exports.factures.pdf-export', [
            'entreprise' => $entreprise ? Entreprise::find($entreprise) : false,
            'debut' => $dateDebut,
            'fin' => $dateFin,
            'factures' => $factures
        ]);
    }

    public function exportFactureExcel(?string $dateDebut, ?string $dateFin, ?int $entreprise)
    {
        $name = sprintf('facture_%s_%s_%s.xlsx', $dateDebut, $dateFin, $entreprise);
        return Excel::download(new FactureExport($dateDebut, $dateFin, $entreprise), $name);
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

    public function exportRecapForPilote(array $period, Pilote $pilote)
    {
        $reservations = Reservation::where('pilote_id', $pilote->id)
            ->whereIn('statut', [
                ReservationStatus::CanceledToPay->value,
                ReservationStatus::Confirmed->value,
                ReservationStatus::Billed->value
            ])
            ->whereDate('pickup_date', '>=', $period[0]->format('Y-m-d'))
            ->whereDate('pickup_date', '<=', $period[1]->format('Y-m-d'))
            ->orderBy('pickup_date')
            ->get();

        return Pdf::loadView('exports.pilote.pdf-recap-reservation', [
            'pilote' => $pilote,
            'period' => $period,
            'reservations' => $reservations
        ]);
    }
}

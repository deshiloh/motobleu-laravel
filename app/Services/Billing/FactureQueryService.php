<?php

namespace App\Services\Billing;

use App\Enum\BillStatut;
use App\Enum\ReservationStatus;
use App\Models\Facture;
use App\Models\Entreprise;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FactureQueryService
{
    public function getFacturesForExport(
        ?string $dateDebut = null,
        ?string $dateFin = null,
        ?int $entrepriseId = null,
        int $perPage = 30
    ): LengthAwarePaginator {
        return Facture::has('reservations')
            ->whereIn('statut', [BillStatut::COMPLETED, BillStatut::CANCEL])
            ->orderBy('id', 'DESC')
            ->when($dateDebut && $dateFin, function (Builder $query) use ($dateDebut, $dateFin) {
                return $this->applyDateRangeFilter($query, $dateDebut, $dateFin);
            })
            ->when($entrepriseId, function (Builder $query) use ($entrepriseId) {
                return $this->applyEntrepriseFilter($query, $entrepriseId);
            })
            ->paginate($perPage);
    }

    public function getFacturesForDataTable(
        string $search = '',
        ?int $entrepriseId = null,
        ?int $isAcquitte = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Facture::has('reservations')
            ->when($search, function (Builder $query) use ($search) {
                return $query->where('reference', 'like', '%' . $search . '%');
            })
            ->when($entrepriseId, function (Builder $query) use ($entrepriseId) {
                return $this->applyEntrepriseFilter($query, $entrepriseId);
            })
            ->when($isAcquitte > 0, function (Builder $query) use ($isAcquitte) {
                return match ($isAcquitte) {
                    1 => $query->where('is_acquitte', false),
                    2 => $query->where('is_acquitte', true)
                };
            })
            ->orderBy('factures.id', 'desc')
            ->paginate($perPage);
    }

    public function getEntreprisesToBill(int $month, int $year, ?int $entrepriseId = null): Collection|array
    {
        return Entreprise::orderBy('nom')
            ->whereHas('reservations', function (Builder $query) use ($month, $year) {
                return $this->applyBillableReservationsFilter($query, $month, $year);
            })
            ->withCount([
                'reservations' => function (Builder $query) use ($month, $year) {
                    return $this->applyBillableReservationsFilter($query, $month, $year);
                }
            ])
            ->when($entrepriseId, function (Builder $query) use ($entrepriseId) {
                return $query->where('id', $entrepriseId);
            })
            ->get();
    }

    public function findExistingFacture(int $month, int $year, int $entrepriseId): ?Facture
    {
        return Facture::where('month', $month)
            ->where('year', $year)
            ->whereHas('reservations', function (Builder $query) use ($entrepriseId) {
                return $query->where('entreprise_id', $entrepriseId);
            })
            ->where('is_acquitte', false)
            ->where('statut', BillStatut::CREATED->value)
            ->orderBy('id', 'desc')
            ->first();
    }

    private function applyDateRangeFilter(Builder $query, string $dateDebut, string $dateFin): Builder
    {
        $dateDebut = Carbon::createFromFormat("Y-m-d", $dateDebut);
        $dateFin = Carbon::createFromFormat("Y-m-d", $dateFin);

        $months = [];
        $years = [];

        for ($currentMonth = $dateDebut->month; $currentMonth <= $dateFin->month; $currentMonth++) {
            $months[] = $currentMonth;
        }

        for ($currentYear = $dateDebut->year; $currentYear <= $dateFin->year; $currentYear++) {
            $years[] = $currentYear;
        }

        return $query
            ->whereIn('year', $years)
            ->whereIn('month', $months);
    }

    private function applyEntrepriseFilter(Builder $query, int $entrepriseId): Builder
    {
        return $query->whereHas('reservations', function (Builder $query) use ($entrepriseId) {
            return $query->where('entreprise_id', $entrepriseId);
        });
    }

    private function applyBillableReservationsFilter(Builder $query, int $month, int $year): Builder
    {
        return $query
            ->whereMonth('pickup_date', $month)
            ->whereYear('pickup_date', $year)
            ->whereIn('statut', [ReservationStatus::Confirmed->value, ReservationStatus::CanceledToPay->value])
            ->where(function (Builder $query) {
                $query
                    ->whereNull('encaisse_pilote')
                    ->orWhere('encaisse_pilote', 0);
            });
    }
}
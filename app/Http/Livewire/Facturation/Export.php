<?php

namespace App\Http\Livewire\Facturation;

use App\Enum\BillStatut;
use App\Models\Facture;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Export extends Component
{
    use WithPagination;

    public ?string $dateDebut = null;
    public ?string $dateFin = null;
    public ?int $entreprise = null;
    public int $perPage = 30;


    public $queryString = [
        'dateDebut' => ["except" => null],
        'dateFin' => ["except" => null],
        'entreprise' => ["except" => null],
        'perPage',
    ];

    public function mount(): void
    {
        $this->dateDebut = now()->startOfMonth()->format('Y-m-d');
        $this->dateFin = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.facturation.export', [
            'factures' => $this->getFactures()
        ])
            ->layout('components.layout');
    }

    public function exportAction(ExportService $exportService)
    {
        return response()->streamDownload(function () use ($exportService) {
            echo $exportService->exportFactures($this->dateDebut, $this->dateFin, $this->entreprise)->output();
        }, 'export_factures.pdf');
    }

    public function exportExcelAction(ExportService $exportService)
    {
        return $exportService->exportFactureExcel(
            $this->dateDebut,
            $this->dateFin,
            $this->entreprise
        );
    }

    private function getFactures(): LengthAwarePaginator
    {
        return Facture::has('reservations')
            ->whereIn('statut', [BillStatut::COMPLETED, BillStatut::CANCEL])
            ->orderBy('id', 'DESC')
            ->when($this->dateDebut && $this->dateFin, function(Builder $query)  {
                $dateDebut = Carbon::createFromFormat("Y-m-d", $this->dateDebut);
                $dateFin = Carbon::createFromFormat("Y-m-d", $this->dateFin);

                $months = [];
                $years = [];

                for ($currentMonth = $dateDebut->month; $currentMonth <= $dateFin->month; $currentMonth ++) {
                    $months[] = $currentMonth;
                }

                for ($currentYear = $dateDebut->year; $currentYear <= $dateFin->year; $currentYear ++) {
                    $years[] = $currentYear;
                }


                return $query
                    ->whereIn('year', $years)
                    ->whereIn('month', $months);
            })
            ->when($this->entreprise, function(Builder $query) {
                return $query->whereHas('reservations', function (Builder $query) {
                    return $query->where('entreprise_id', $this->entreprise);
                });
            })
            ->paginate($this->perPage);
    }
}

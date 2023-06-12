<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Services\ExportService;
use App\Services\SentryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ReservationController extends Controller
{
    /**
     * Show list of reservations
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.reservation.index');
    }

    /**
     * Show create reservation form
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.reservation.create');
    }

    public function export(ExportService $exportService)
    {
        return $exportService->exportReservations(2022, 8, Entreprise::find(1));
    }
}

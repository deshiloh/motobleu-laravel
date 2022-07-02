<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
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
}

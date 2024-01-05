<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function reservationsShow()
    {
        return view('admin.stats.reservations-view');
    }

    public function facturationShow()
    {
        return view('admin.stats.facturation-view');
    }
}

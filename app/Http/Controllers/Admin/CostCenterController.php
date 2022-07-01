<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.costcenter.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CostCenter $costcenter
     * @return RedirectResponse
     */
    public function destroy(CostCenter $costcenter)
    {
        $costcenter->delete();
        return redirect()
            ->route('admin.costcenter.index')
            ->with('success', "Le Cost Center a bien été supprimé.");
    }
}

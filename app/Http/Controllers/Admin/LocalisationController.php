<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Localisation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LocalisationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.localisation.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Localisation $localisation
     * @return RedirectResponse
     */
    public function destroy(Localisation $localisation)
    {
        $localisation->update([
            'is_actif' => false
        ]);
        return redirect()
            ->route('admin.localisations.index')
            ->with('success', "La localisation a bien été supprimée.");
    }
}

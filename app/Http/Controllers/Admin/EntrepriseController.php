<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EntrepriseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        return view('admin.entreprise.index', [
            'entreprises' => Entreprise::all()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Entreprise $entreprise
     * @return Application|Factory|View
     */
    public function show(Entreprise $entreprise): View|Factory|Application
    {
        return view('admin.entreprise.show', [
            'entreprise' => $entreprise
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Entreprise $entreprise
     * @return RedirectResponse
     */
    public function destroy(Entreprise $entreprise): RedirectResponse
    {
        $entreprise->is_actif = false;
        $entreprise->save();
        return redirect()
            ->route('admin.entreprises.index')
            ->with('success', "L'entreprise a bien été désactivée.");
    }
}

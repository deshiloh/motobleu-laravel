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
    public function index()
    {
        return view('admin.entreprise.index', [
            'entreprises' => Entreprise::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.entreprise.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string'
        ]);

        Entreprise::create($request->only('nom'));

        return redirect()
            ->route('admin.entreprises.index')
            ->with('success', "Entreprise correctement créée");
    }

    /**
     * Display the specified resource.
     *
     * @param Entreprise $entreprise
     * @return Application|Factory|View
     */
    public function show(Entreprise $entreprise)
    {
        return view('admin.entreprise.show', [
            'entreprise' => $entreprise
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Entreprise $entreprise
     * @return Application|Factory|View
     */
    public function edit(Entreprise $entreprise)
    {
        return view('admin.entreprise.edit', [
            'entreprise' => $entreprise
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Entreprise $entreprise
     * @return RedirectResponse
     */
    public function update(Request $request, Entreprise $entreprise)
    {
        $request->validate([
            'nom' => 'required|string'
        ]);
        $entreprise->update((array) $request->input());
        $entreprise->save();
        return back()
            ->with('success', "L'entreprise a bien été modifiée.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Entreprise $entreprise
     * @return RedirectResponse
     */
    public function destroy(Entreprise $entreprise)
    {
        $entreprise->is_actif = false;
        $entreprise->save();
        return redirect()
            ->route('admin.entreprises.index')
            ->with('success', "L'entreprise a bien été désactivée.");
    }
}

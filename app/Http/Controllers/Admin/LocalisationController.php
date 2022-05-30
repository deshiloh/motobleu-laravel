<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocalisationRequest;
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
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.localisation.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LocalisationRequest $request
     * @return RedirectResponse
     */
    public function store(LocalisationRequest $request)
    {
        $request->validated();

        $datas = (array) $request->input();

        if (!$request->has('is_actif')) $datas['is_actif'] = false;

        Localisation::create($datas);

        return redirect()
            ->route('admin.localisations.store')
            ->with('success', "La localisation a bien été créée.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Localisation $localisation
     * @return Application|Factory|View
     */
    public function edit(Localisation $localisation)
    {
        return view('admin.localisation.form', [
            'localisation' => $localisation
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LocalisationRequest $request
     * @param Localisation $localisation
     * @return RedirectResponse
     */
    public function update(LocalisationRequest $request, Localisation $localisation)
    {
        $request->validated();

        $datas = (array) $request->input();

        if (!$request->has('is_actif')) $datas['is_actif'] = false;

        $localisation->update($datas);

        return redirect()
            ->route('admin.localisations.edit', ['localisation' => $localisation->id])
            ->with('success', "La localisation a bien été modifiée.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Localisation $localisation
     * @return RedirectResponse
     */
    public function destroy(Localisation $localisation)
    {
        $localisation->delete();
        return redirect()
            ->route('admin.localisations.index')
            ->with('success', "La localisation a bien été supprimée.");
    }
}

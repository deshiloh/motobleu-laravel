<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PiloteRequest;
use App\Models\Pilote;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PiloteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.pilotes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.pilotes.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PiloteRequest $request
     * @return RedirectResponse
     */
    public function store(PiloteRequest $request)
    {
        $request->validated();

        Pilote::create($request->input());

        return redirect()
            ->route('admin.pilotes.index')
            ->with('success', "Le pilote a bien été créé.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Pilote $pilote
     * @return Application|Factory|View
     */
    public function edit(Pilote $pilote)
    {
        return view('admin.pilotes.form', [
            'pilote' => $pilote
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PiloteRequest $request
     * @param Pilote $pilote
     * @return RedirectResponse
     */
    public function update(PiloteRequest $request, Pilote $pilote)
    {
        $request->validated();

        $pilote->update($request->input());

        return back()
            ->with('success', "Le pilote a bien été mise à jour.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Pilote $pilote
     * @return RedirectResponse
     */
    public function destroy(Pilote $pilote)
    {
        $pilote->delete();
        return redirect()
            ->route('admin.pilotes.index')
            ->with('success', "Le pilote a bien été supprimé.");
    }
}

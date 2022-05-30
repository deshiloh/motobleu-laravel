<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TypeFacturationRequest;
use App\Models\Entreprise;
use App\Models\TypeFacturation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypeFacturationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.typefacturation.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.typefacturation.form', [
            'entreprises' => $this->getEntreprisesSelectDatas()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TypeFacturationRequest $request
     * @return RedirectResponse
     */
    public function store(TypeFacturationRequest $request)
    {
        $request->validated();

        TypeFacturation::create($request->input());

        return redirect()
            ->route('admin.typefacturation.index')
            ->with('success', "Le type de facturation a bien été créé.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TypeFacturation $typefacturation
     * @return Application|Factory|View
     */
    public function edit(TypeFacturation $typefacturation)
    {
        return view('admin.typefacturation.form', [
            'typefacturation' => $typefacturation,
            'entreprises' => $this->getEntreprisesSelectDatas()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TypeFacturationRequest $request
     * @param TypeFacturation $typefacturation
     * @return RedirectResponse
     */
    public function update(TypeFacturationRequest $request, TypeFacturation $typefacturation)
    {
        $request->validated();

        $typefacturation->update($request->input());

        return to_route('admin.typefacturation.edit', [
            'typefacturation' => $typefacturation->id
        ])->with('success', "Le type de facturation a bien été modifié.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TypeFacturation $typefacturation
     * @return RedirectResponse
     */
    public function destroy(TypeFacturation $typefacturation)
    {
        $typefacturation->delete();
        return to_route('admin.typefacturation.index')
            ->with('success', "Le type de facturation a bien été supprimé.");
    }

    /**
     * @return array
     */
    public function getEntreprisesSelectDatas(): array
    {
        $selectDatas = [];

        foreach (Entreprise::all() as $data) {
            $selectDatas[$data->id] = $data->nom;
        }
        return $selectDatas;
    }
}

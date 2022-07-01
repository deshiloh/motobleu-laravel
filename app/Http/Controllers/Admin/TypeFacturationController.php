<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use App\Models\TypeFacturation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Passager;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PassagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.passager.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Passager $passager
     * @return RedirectResponse
     */
    public function destroy(Passager $passager)
    {
        $passager->update([
            'is_actif' => false
        ]);
        return redirect()
            ->route('admin.passagers.index')
            ->with('success', "Le passager a bien été supprimé.");
    }

    /**
     * @return array
     */
    public function getUsersSelectDatas(): array
    {
        $selectDatas = [];

        foreach (User::all() as $data) {
            $selectDatas[$data->id] = $data->nom;
        }
        return $selectDatas;
    }
}

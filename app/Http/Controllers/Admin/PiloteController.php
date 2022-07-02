<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pilote;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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

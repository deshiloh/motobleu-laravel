<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CostCenterRequest;
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
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.costcenter.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CostCenterRequest $request
     * @return RedirectResponse
     */
    public function store(CostCenterRequest $request)
    {
        $request->validated();

        $datas = $request->input();

        if (!$request->has('is_actif')) $datas['is_actif'] = false;

        CostCenter::create($datas);

        return redirect()
            ->route('admin.costcenter.index')
            ->with('success', "Le Cost Center a bien été créé.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CostCenter $costcenter
     * @return Application|Factory|View
     */
    public function edit(CostCenter $costcenter)
    {
        return view('admin.costcenter.form',[
            'costcenter' => $costcenter
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CostCenterRequest $request
     * @param CostCenter $costcenter
     * @return RedirectResponse
     */
    public function update(CostCenterRequest $request, CostCenter $costcenter)
    {
        $request->validated();

        $datas = $request->input();

        if (!$request->has('is_actif')) $datas['is_actif'] = false;

        $costcenter->update($datas);

        return redirect()
            ->route('admin.costcenter.edit', ['costcenter' => $costcenter->id])
            ->with('success', "Le Cost Center a bien été créé.");
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PassagerRequest;
use App\Models\Passager;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery\Generator\StringManipulation\Pass\Pass;

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
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.passager.form', [
            'users' => $this->getUsersSelectDatas()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PassagerRequest $request
     * @return RedirectResponse
     */
    public function store(PassagerRequest $request)
    {
        $request->validated();

        /** @var User $user */
        $user = User::find($request->input('user_id'));
        $user->passagers()->create($request->input());

        return redirect()
            ->route('admin.passagers.index')
            ->with('success', "Le passager a bien été créé.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Passager $passager
     * @return Application|Factory|View
     */
    public function edit(Passager $passager)
    {
        return view('admin.passager.form', [
            'passager' => $passager,
            'users' => $this->getUsersSelectDatas()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PassagerRequest $request
     * @param Passager $passager
     * @return RedirectResponse
     */
    public function update(PassagerRequest $request, Passager $passager)
    {
        $request->validated();

        $passager->update($request->input());

        return redirect()
            ->route('admin.passagers.edit', ['passager' => $passager])
            ->with('success', "Le passager a bien été modifié.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Passager $passager
     * @return RedirectResponse
     */
    public function destroy(Passager $passager)
    {
        $passager->delete();
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

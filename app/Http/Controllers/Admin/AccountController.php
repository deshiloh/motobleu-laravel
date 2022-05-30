<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin.account.index', [
            'users' => User::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.account.create', ['account' => false]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AccountRequest $request
     * @return RedirectResponse
     */
    public function store(AccountRequest $request)
    {
        $request->validated();
        /** @var array $datas */
        $datas = $request->input();
        $datas['password'] = Hash::make('test');

        $user = User::create($datas);

        if ($request->has('entreprise')) {
            $entreprise = Entreprise::find($request->input('entreprise'));
            $user->entreprise()->associate($entreprise);
            $user->save();
        }
        return redirect()
            ->route('admin.accounts.index')
            ->with('success', "Utilisateur créé");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $account
     * @return Application|Factory|View
     */
    public function edit(User $account)
    {
        return view('admin.account.create', [
            'account' => $account
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AccountRequest $request
     * @param User $account
     * @return RedirectResponse
     */
    public function update(AccountRequest $request, User $account)
    {
        $request->validated();

        $account->update((array) $request->input());

        $entreprise = Entreprise::find($request->input('entreprise'));
        $account->entreprise()->associate($entreprise);

        $account->save();

        return redirect()
            ->route('admin.accounts.edit', ['account' => $account->id])
            ->with('success', "L'utilisateur a bien été mit à jour.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $account
     * @return RedirectResponse
     */
    public function destroy(User $account)
    {
        $account->is_actif = false;
        $account->save();
        return redirect()
            ->route('admin.accounts.index')
            ->with('success', "L'utilisateur a bien été supprimé.");
    }
}

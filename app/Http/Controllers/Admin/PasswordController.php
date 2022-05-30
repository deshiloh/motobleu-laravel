<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * @param User $account
     * @return Application|Factory|View
     */
    public function edit(User $account)
    {
        return view('admin.account.password.edit', ['account' => $account]);
    }

    /**
     * @param User $account
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(User $account, Request $request)
    {
        $request->validate([
            'password' => ['min:2']
        ]);

        $account->password = Hash::make((string) $request->input('password'));
        $account->save();
        return redirect()
            ->route('admin.accounts.index')
            ->with('success', "Le mot de passe a bien été changé.");
    }
}

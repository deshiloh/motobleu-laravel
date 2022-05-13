<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit(User $account)
    {
        return view('admin.account.password.edit', ['account' => $account]);
    }

    public function update(User $account, Request $request)
    {
        $request->validate([
            'password' => ['min:2']
        ]);

        $account->password = Hash::make($request->input('password'));
        $account->save();
        return redirect()
            ->route('admin.accounts.index')
            ->with('success', "Le mot de passe a bien été changé.");
    }
}

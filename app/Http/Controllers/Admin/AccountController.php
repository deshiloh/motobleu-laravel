<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entreprise;
use App\Models\User;
use App\Services\SentryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Sentry\Severity;
use Sentry\State\Scope;
use function Sentry\captureMessage;
use function Sentry\configureScope;
use function Sentry\withScope;

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

<?php

use App\Models\CostCenter;
use App\Models\Entreprise;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\TypeFacturation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', function (Request $request){
    $search = $request->input('search');
    $selected = $request->input('selected');

    return User::query()
        ->select('id', 'nom', 'email', 'prenom', 'entreprise_id')
        ->with('entreprise')
        ->orderBy('nom')
        ->when(
            $search, function (Builder $query, $search) {
            $query
                ->where('nom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('prenom', 'like', "%{$search}%")
                ->orWhereHas('entreprise', function ($query) use ($search) {
                    $query->where('nom', 'like', "%{$search}%");
                });
            }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.users');

Route::get('/cost-center', function (Request $request){
    $search = $request->input('search');
    $selected = $request->input('selected');

    return CostCenter::query()
        ->select('id', 'nom')
        ->orderBy('nom')
        ->when(
            $search, function (Builder $query, $search) {
                $query->where('nom', 'like', "%{$search}%");
            }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.cost_center');

Route::get('/type-facturation', function (Request $request){
    $search = $request->input('search');
    $selected = $request->input('selected');

    return TypeFacturation::query()
        ->select('id', 'nom')
        ->orderBy('nom')
        ->when(
            $search, function (Builder $query, $search) {
            $query->where('nom', 'like', "%{$search}%");
        }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.type_facturation');

Route::get('/pilotes', function (Request $request) {
    $search = $request->input('search');
    $selected = $request->input('selected');

    return Pilote::query()
        ->select('id', 'nom', 'email', 'prenom')
        ->orderBy('nom')
        ->when(
            $search, function (Builder $query, $search) {
            $query->where('nom', 'like', "%{$search}%");
            $query->orWhere('prenom', 'like', "%{$search}%");
        }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.pilotes');

Route::get('/entreprises', function (Request $request) {
    $search = $request->input('search');
    $selected = $request->input('selected');

    return Entreprise::query()
        ->select('id', 'nom')
        ->orderBy('nom')
        ->when(
            $search, function (Builder $query, $search) {
            $query->where('nom', 'like', "%{$search}%");
        }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.entreprises');

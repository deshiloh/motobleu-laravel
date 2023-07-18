<?php

use App\Events\ReservationConfirmed;
use App\Http\Controllers\ApiController;
use App\Http\Resources\ReservationResource;
use App\Mail\PiloteAttached;
use App\Mail\PiloteDetached;
use App\Models\AdresseReservation;
use App\Models\CostCenter;
use App\Models\Entreprise;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Models\TypeFacturation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ["L'email ou le mot de passe n'est pas valide"],
        ]);
    }

    $user->tokens()->where('name', $request->device_name)->delete();

    return new JsonResponse([
        'token' => $user->createToken($request->device_name)->plainTextToken,
        'email' => $user->email,
        'surname' => $user->nom,
        'name' => $user->prenom
    ]);
});

Route::middleware("auth:sanctum")->group(function() {
    Route::get("/reservations", function (Request $request) {
        $statut = $request->get("statut");
        $currentDate = Carbon::now();
        $twoMonthsAgo = $currentDate->subMonths(1);

        return ReservationResource::collection(
            Reservation::whereHas('passager.user')
                ->when($request->has('statut'), function(Builder $query) use ($statut) {
                    return $query->where('statut', \App\Enum\ReservationStatus::from($statut));
                })
                //->whereDate('pickup_date', '<', $twoMonthsAgo)
                ->orderBy('id', 'desc')
                ->get()
        );
    });

    Route::get("/search", function (Request $request) {
        $search = $request->get("search");

        if (!$request->has('search') || empty($search)) {
            return new JsonResponse([]);
        }

        return ReservationResource::collection(
            Reservation::whereHas('passager.user')
                ->when($request->has('search'), function (Builder $query) use ($search) {
                    $query
                        ->where('reference', 'like', '%' . $search . '%')
                        ->orWhere(function(Builder $query) use ($search) {
                            return $query->whereHas('entreprise', function (Builder $query) use ($search) {
                                return $query->where('nom', 'like', '%' . $search . '%');
                            });
                        });
                })
                ->orderBy('id', 'desc')
                ->limit(400)
                ->get()
        );
    });

    Route::get("/pilotes", function(Request $request) {
        return \App\Http\Resources\PiloteResource::collection(Pilote::orderBy('nom')->get());
    });

    Route::post('/reservation/{reservation}/{action}', [ApiController::class, 'handleAction']);
});

Route::get("/test", function () {
    return new JsonResponse([
        'response' => 'test'
    ]);
})->middleware(['auth:sanctum']);

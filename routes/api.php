<?php

use App\Models\AdresseReservation;
use App\Models\CostCenter;
use App\Models\Entreprise;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\TypeFacturation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user->tokens()->where('name', $request->device_name)->delete();

    return new JsonResponse([
        'token' => $user->createToken($request->device_name)->plainTextToken
    ]);
});

Route::get("/test", function () {
    return new JsonResponse([
        'response' => 'test'
    ]);
})->middleware(['auth:sanctum']);

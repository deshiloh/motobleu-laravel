<?php

use App\Http\Controllers\LoginController;
use App\Http\Livewire\Auth\ForgotPasswordForm;
use App\Mail\AdminReservationConfirmed;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login.form');

    Route::post('/login', [LoginController::class, 'authenticate'])
        ->name('login');

    Route::get('/forgot-password', ForgotPasswordForm::class)
        ->name('password.request');

    Route::get('/reset-password/{token}', [LoginController::class, 'resetPasswordEdit'])
        ->name('password.reset');

    Route::post('/reset-password', [LoginController::class, 'resetPassword'])
        ->name('password.update');

    Route::get('/pages/{slug}', function (string $slug) {
        $page = \App\Models\Page::whereLocale('slug', App::getLocale())->firstOrFail();
        return view('front.pages', [
            'page' => $page
        ]);
    })->name('pages');
});

Route::get('/account/new', \App\Http\Livewire\Front\NewAccountForm::class)
    ->name('account.new');


Route::get('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::get('/', function () {
    if (Auth::check()) {
        return to_route('front.reservation.list');
    }

    return view('front.home');
})->name('front.home');

Route::get('/lang/{locale}', function (string $locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);

    return redirect()->back();
})->name('switch.local');

Route::get('/oauth', function(Request $request) {
    $code =  $request->get('code');

    return new \Illuminate\Http\JsonResponse([
        'code' => $code
    ]);
});

Route::get('/mail-test', function () {
    if (!App::environment(['local'])) {
        abort(403, "Pas autorisé");
    }

    $reservation = Reservation::take(1)->first();

    return new \App\Mail\ReservationConfirmed($reservation, false);
});

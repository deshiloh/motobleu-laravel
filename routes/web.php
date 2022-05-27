<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdresseEntrepriseController;
use App\Http\Controllers\Admin\EntrepriseController;
use App\Http\Controllers\Admin\LocalisationController;
use App\Http\Controllers\Admin\PassagerController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\PiloteController;
use App\Http\Controllers\LoginController;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
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

Route::get('/', function () {
    return view('welcome');
})->name('homepage');

Route::get('/login', function () {
    return view('welcome');
})
    ->name('login.form');

Route::post('/login', [LoginController::class, 'authenticate'])
    ->name('login');
Route::get('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [LoginController::class, 'forgotPasswordEdit'])
        ->name('password.request');

    Route::post('/forgot-password', [LoginController::class, 'forgotPassword'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [LoginController::class, 'resetPasswordEdit'])
        ->name('password.reset');

    Route::post('/reset-password', [LoginController::class, 'resetPassword'])
        ->name('password.update');
});

Route::prefix('/admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/accounts/{account}/password/edit', [PasswordController::class, 'edit'])->name('accounts.password.edit');
    Route::put('/accounts/{account}/password', [PasswordController::class, 'update'])->name('accounts.password.update');

    Route::resource('accounts', AccountController::class)->except(['show']);

    Route::resources([
        'entreprises' => EntrepriseController::class,
    ]);

    Route::resource('entreprises.adresses', AdresseEntrepriseController::class)
        ->except(['show']);

    Route::resource('passagers', PassagerController::class)
        ->except(['show']);

    Route::resource('pilotes', PiloteController::class)
        ->except(['show']);

    Route::resource('localisations', LocalisationController::class)
        ->except(['show']);
});

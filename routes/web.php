<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdresseEntrepriseController;
use App\Http\Controllers\Admin\CostCenterController;
use App\Http\Controllers\Admin\EntrepriseController;
use App\Http\Controllers\Admin\FacturationsController;
use App\Http\Controllers\Admin\LocalisationController;
use App\Http\Controllers\Admin\PassagerController;
use App\Http\Controllers\Admin\PiloteController;
use App\Http\Controllers\Admin\TypeFacturationController;
use App\Http\Controllers\AdresseReservationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReservationController;
use App\Http\Livewire\Account\AccountForm;
use App\Http\Livewire\Account\EditPasswordForm;
use App\Http\Livewire\Account\Form;
use App\Http\Livewire\AdresseReservation\AdresseReservationForm;
use App\Http\Livewire\Auth\ForgotPasswordForm;
use App\Http\Livewire\CostCenter\CostCenterForm;
use App\Http\Livewire\Entreprise\AdresseEntrepriseForm;
use App\Http\Livewire\Entreprise\EntrepriseForm;
use App\Http\Livewire\Facturation\EditionFacture;
use App\Http\Livewire\Facturation\FacturationDataTable;
use App\Http\Livewire\Front\Reservation\ReservationDataTable;
use App\Http\Livewire\Localisation\LocalisationForm;
use App\Http\Livewire\Passager\PassagerForm;
use App\Http\Livewire\Pilote\PiloteForm;
use App\Http\Livewire\Pilote\RecapReservationPilote;
use App\Http\Livewire\Reservation\ReservationForm;
use App\Http\Livewire\ReservationEdit;
use App\Http\Livewire\Reservation\ReservationShow;
use App\Http\Livewire\TypeFacturation\TypeFacturationForm;
use App\Mail\AdminReservationConfirmed;
use App\Models\AdresseReservation;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
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

Route::get('/lang/{locale}', function (string $locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);

    return redirect()->back();
})->name('switch.local');

/*
|--------------------------------------------------------------------------
| FRONT Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard')->name('front.')->group(function () {
    Route::get('/', function () {
        return view('front.dashboard');
    })->name('dashboard');

    Route::prefix('/reservation')->name('reservation.')->group(function () {
        Route::get('/', ReservationDataTable::class)
            ->name('list');
        Route::get('/create', \App\Http\Livewire\Front\Reservation\ReservationForm::class)
            ->name('create');
    });

    Route::prefix('/address')->name('address.')->group(function () {
        Route::get('/list', \App\Http\Livewire\Front\Address\AddressDataTable::class)
            ->name('list');
        Route::get('/create', \App\Http\Livewire\Front\Address\AddressForm::class)
            ->name('create');
        Route::get('/{address}/edit', \App\Http\Livewire\Front\Address\AddressForm::class)
            ->name('edit');
    });

    Route::prefix('/passager')->name('passager.')->group(function () {
        Route::get('/list', \App\Http\Livewire\Front\Passager\PassagerDataTable::class)
            ->name('list');
        Route::get('/create', \App\Http\Livewire\Front\Passager\PassagerForm::class)
            ->name('create');
        Route::get('/{passager}/edit', \App\Http\Livewire\Front\Passager\PassagerForm::class)
            ->name('edit');
    });

    // DISPONIBLE UNIQUEMENT POUR ARDIAN
    Route::prefix('/secretary')->name('user.')->group(function () {
       Route::get('/list', \App\Http\Livewire\Front\Account\AccountDataTable::class)
           ->name('list');
       Route::get('/create', \App\Http\Livewire\Front\Account\AccountForm::class)
           ->name('create');
        Route::get('/{account}/edit', \App\Http\Livewire\Front\Account\AccountForm::class)
            ->name('edit');
    });
});

Route::get('/logout', [LoginController::class, 'logout'])
    ->name('logout');

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
});

Route::get('/', function () {
    if (Auth::check()) {
        return to_route('front.dashboard');
    }

    return view('front.home');
})->name('front.home');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('/admin')->middleware('auth')->name('admin.')->group(function () {

    // Accueil de la partie admin
    Route::get('/dashboard', function () {
        return view('welcome', [
            'reservations_to_confirm' => Reservation::toConfirmed(),
            'reservations' => Reservation::count(),
            'users' => User::count()
        ]);
    })->name('homepage');

    Route::get('/accounts/{account}/password/edit', EditPasswordForm::class)
        ->name('accounts.password.edit');

    // ACCOUNTS
    Route::get('accounts/create', AccountForm::class)
        ->name('accounts.create');
    Route::get('accounts/{account}/edit', AccountForm::class)
        ->name('accounts.edit');
    Route::resource('accounts', AccountController::class)
        ->except(['show', 'create', 'store', 'edit', 'update']);

    // ENTREPRISE
    Route::get('entreprises/create', EntrepriseForm::class)
        ->name('entreprises.create');
    Route::get('entreprises/{entreprise}/edit', EntrepriseForm::class)
        ->name('entreprises.edit');
    Route::resource('entreprises', EntrepriseController::class)
        ->except('create', 'store', 'edit', 'update');

    // ADRESSE ENTREPRISE
    Route::get('entreprises/{entreprise}/adresses/create', AdresseEntrepriseForm::class)
        ->name('entreprises.adresses.create');
    Route::get('entreprises/{entreprise}/adresses/{adress}/edit', AdresseEntrepriseForm::class)
        ->name('entreprises.adresses.edit');
    Route::resource('entreprises.adresses', AdresseEntrepriseController::class)
        ->except(['show', 'create', 'store', 'edit', 'update']);

    // PASSAGER
    Route::get('passagers/create', PassagerForm::class)->name('passagers.create');
    Route::get('passagers/{passager}/edit', PassagerForm::class)->name('passagers.edit');
    Route::resource('passagers', PassagerController::class)
        ->except(['show', 'create', 'store', 'edit', 'update']);

    // PILOTE
    Route::get('pilotes/create', PiloteForm::class)
        ->name('pilotes.create');
    Route::get('pilotes/{pilote}/edit', PiloteForm::class)
        ->name('pilotes.edit');
    Route::get('pilotes/{pilote}/recap-reservation', RecapReservationPilote::class)
        ->name('pilotes.recap-reservation');
    Route::resource('pilotes', PiloteController::class)
        ->except(['show', 'create', 'store', 'edit', 'update']);

    // LOCALISATION
    Route::get('localisations/create', LocalisationForm::class)
        ->name('localisations.create');
    Route::get('localisations/{localisation}/edit', LocalisationForm::class)
        ->name('localisations.edit');
    Route::resource('localisations', LocalisationController::class)
        ->except(['show', 'create', 'store', 'edit', 'update']);

    // COST CENTER
    Route::get('costcenter/create', CostCenterForm::class)->name('costcenter.create');
    Route::get('costcenter/{costCenter}/edit', CostCenterForm::class)->name('costcenter.edit');
    Route::resource('costcenter', CostCenterController::class)
        ->except(['show', 'create', 'store', 'edit', 'update']);

    // TYPE FACTURATION
    Route::get('typefacturation/create', TypeFacturationForm::class)
        ->name('typefacturation.create');
    Route::get('typefacturation/{typefacturation}/edit', TypeFacturationForm::class)
        ->name('typefacturation.edit');
    Route::resource('typefacturation', TypeFacturationController::class)
        ->except(['show', 'create', 'store', 'edit', 'update']);

    // ADRESSE RESERVATION
    Route::get('adresse-reservation/create', AdresseReservationForm::class)
        ->name('adresse-reservation.create');
    Route::get('adresse-reservation/{adresseReservation}/edit', AdresseReservationForm::class)
        ->name('adresse-reservation.edit');
    Route::resource('adresse-reservation', AdresseReservationController::class)
        ->except(['show', 'create', 'store', 'edit', 'update']);

    // RESERVATION
    Route::get('reservations/show/{reservation}', ReservationShow::class)
        ->name('reservations.show');
    Route::get('reservations/create', ReservationForm::class)
        ->name('reservations.create');
    Route::get('reservations/{reservation}/edit', ReservationForm::class)
        ->name('reservations.edit');
    Route::resource('reservations', ReservationController::class)
        ->except(['show', 'update', 'destroy', 'edit', 'create', 'store']);
    Route::get('reservations/export', [ReservationController::class, 'export'])->name('reservations.export');

    // FACTURATIONS
    Route::get('facturations', FacturationDataTable::class)
        ->name('facturations.index');
    Route::get('facturations/edition', EditionFacture::class)
        ->name('facturations.edition');
    Route::get('facturations/{facture}/show', [FacturationsController::class, 'show'])
        ->name('facturations.show');
});

Route::prefix('/admin/select')->group(function () {

    // API PASSAGER
    Route::get('/passagers', function (Request $request){
        $search = $request->input('search');
        $selected = $request->input('selected');
        $user = $request->input('user');

        $result = Passager::query()
            ->select('id', 'nom', 'email');

        if (!empty($user)) {
            $result->where('user_id', '=', (int)$user);
        }

        $result->when($search, function (Builder $query, $search) {
            $query->where('nom', 'like', "%{$search}%");
        })->when(
                $selected,
                function (Builder $query, $selected) {
                    $query->whereIn('id', $selected);
                }
            );

        return $result->get();
    })->name('admin.api.passagers');

    // API LOCALISATION
    Route::get('/pickup_place', function (Request $request) {
        $search = $request->input('search');
        $selected = $request->input('selected');

        return Localisation::query()
            ->select('id', 'nom')
            ->orderBy('nom')
            ->when($search, function (Builder $query, $search) {
                $query->where('nom', 'like', "%{$search}%");
            })
            ->when(
                $selected,
                function (Builder $query, $selected) {
                    $query->whereIn('id', $selected);
                }
            )
            ->get();
    })->name('admin.api.pickupplace');

    // API ADRESSE RESERVATION
    Route::get('/adresses', function (Request $request) {
        $search = $request->input('search');
        $selected = $request->input('selected');
        $user = $request->input('user', false);

        return AdresseReservation::query()
            ->select('id', 'adresse')
            ->orderBy('adresse')
            ->when($user, function(Builder $query, $search) {
                $query->where('user_id', $search);
            })
            ->when($search, function (Builder $query, $search) {
                $query->where('adresse', 'like', "%{$search}%");
            })
            ->when(
                $selected,
                function (Builder $query, $selected) {
                    $query->whereIn('id', $selected);
                }
            )
            ->get();
    })->name('admin.api.adresses');

    // API USER ENTREPRISE
    Route::get('/user-entreprise', function (Request $request) {
        $search = $request->input('search');
        $selected = $request->input('selected');

        return User::query()
            ->select('users.*')
            ->orderBy('users.nom')
            ->when($search, function (Builder $query, $search) {
                $query->where('users.nom', 'like', "%{$search}%");
            })
            ->when(
                $selected,
                function (Builder $query, $selected) {
                    $query->whereIn('id', $selected);
                }
            )
            ->get();
    })->name('admin.api.user_in_entreprise');
});

// TESTS PAGES

Route::get('/mailable', function () {
    return new AdminReservationConfirmed(Reservation::find(1));
});

Route::get('/pdf', function () {
    $facture = \App\Models\Facture::find(1);

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.reservations.pdf', [
        'facture' => $facture,
        'entreprise' => $facture->reservations()->get()->first()->entreprise
    ]);
    $pdf->setPaper('A4', 'landscape');

    return $pdf->download('test.pdf');
});



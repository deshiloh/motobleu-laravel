<?php


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

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
use App\Http\Controllers\ReservationController;
use App\Http\Livewire\Account\AccountForm;
use App\Http\Livewire\Account\EditPasswordForm;
use App\Http\Livewire\AdresseReservation\AdresseReservationForm;
use App\Http\Livewire\CostCenter\CostCenterForm;
use App\Http\Livewire\Entreprise\AdresseEntrepriseForm;
use App\Http\Livewire\Entreprise\EntrepriseForm;
use App\Http\Livewire\Facturation\EditionFacture;
use App\Http\Livewire\Facturation\FacturationDataTable;
use App\Http\Livewire\Localisation\LocalisationForm;
use App\Http\Livewire\Passager\PassagerForm;
use App\Http\Livewire\Pilote\PiloteForm;
use App\Http\Livewire\Pilote\RecapReservationPilote;
use App\Http\Livewire\Reservation\ReservationForm;
use App\Http\Livewire\Reservation\ReservationShow;
use App\Http\Livewire\TypeFacturation\TypeFacturationForm;
use App\Models\Reservation;
use App\Models\User;

Route::prefix('/admin')->name('admin.')->group(function () {

    // Accueil de la partie admin
    Route::get('/dashboard', function () {
        return view('welcome', [
            'reservations_to_confirm' => Reservation::toConfirmed()
        ]);
    })->name('homepage');

    Route::get('/accounts/{account}/password/edit', EditPasswordForm::class)
        ->name('accounts.password.edit');

    // ACCOUNTS
    Route::get('accounts/create', AccountForm::class)
        ->name('accounts.create');
    Route::get('accounts/{account}/edit', AccountForm::class)
        ->name('accounts.edit');
    Route::get('accounts/{account}/entreprise', \App\Http\Livewire\Account\EntrepriseForm::class)
        ->name('accounts.entreprise.edit');
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
        ->except(['show', 'create', 'store', 'edit', 'update', 'destroy']);

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
    Route::get('facturations/export', \App\Http\Livewire\Facturation\Export::class)
        ->name('facturations.export');

    Route::get('carousel', \App\Http\Livewire\Carousel\CarouselDataTable::class)
        ->name('carousel');

    Route::get('/pages', \App\Http\Livewire\Pages\PageForm::class)
        ->name('pages');

    Route::get('/permissions', \App\Http\Livewire\Admin\PermissionForm::class)
        ->name('permissions');

    Route::get('/settings', \App\Http\Livewire\Settings\SettingsForm::class)
        ->name('settings');

    Route::get('/stats/reservations', [\App\Http\Controllers\Admin\StatsController::class, 'reservationsShow'])
        ->name('stats.reservations');

    Route::prefix('/stats')->name('stats.')->group(function() {
        Route::get('/reservations', [\App\Http\Controllers\Admin\StatsController::class, 'reservationsShow'])
            ->name('reservations');

        Route::get('/facturation', [\App\Http\Controllers\Admin\StatsController::class, 'facturationShow'])
            ->name('facturation');
    });
});

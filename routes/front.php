<?php


/*
|--------------------------------------------------------------------------
| FRONT Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\FacturationsController;
use App\Http\Livewire\Front\Reservation\ReservationDataTable;

Route::prefix('dashboard')->name('front.')->group(function () {

    Route::get('/', \App\Http\Livewire\Front\DashboardHome::class)
        ->name('dashboard');

    Route::prefix('/reservation')->name('reservation.')->group(function () {
        Route::get('/', ReservationDataTable::class)
            ->middleware('can:see reservation')
            ->name('list');
        Route::get('/create', \App\Http\Livewire\Front\Reservation\ReservationForm::class)
            ->middleware('can:create reservation')
            ->name('create');
    });

    Route::prefix('/address')->name('address.')->group(function () {
        Route::get('/list', \App\Http\Livewire\Front\Address\AddressDataTable::class)
            ->middleware('can:see address reservation')
            ->name('list');
        Route::get('/create', \App\Http\Livewire\Front\Address\AddressForm::class)
            ->middleware('can:create address reservation')
            ->name('create');
        Route::get('/{address}/edit', \App\Http\Livewire\Front\Address\AddressForm::class)
            ->middleware('can:edit address reservation')
            ->name('edit');
    });

    Route::prefix('/passager')->name('passager.')->group(function () {
        Route::get('/list', \App\Http\Livewire\Front\Passager\PassagerDataTable::class)
            ->middleware('can:see passenger')
            ->name('list');
        Route::get('/create', \App\Http\Livewire\Front\Passager\PassagerForm::class)
            ->middleware('can:create passenger')
            ->name('create');
        Route::get('/{passager}/edit', \App\Http\Livewire\Front\Passager\PassagerForm::class)
            ->middleware('can:edit passenger')
            ->name('edit');
    });

    // DISPONIBLE UNIQUEMENT POUR ARDIAN
    Route::prefix('/secretary')->name('user.')->group(function () {
        Route::get('/list', \App\Http\Livewire\Front\Account\AccountDataTable::class)
            ->middleware('can:see user')
            ->name('list');
        Route::get('/create', \App\Http\Livewire\Front\Account\AccountForm::class)
            ->middleware('can:create user')
            ->name('create');
        Route::get('/{account}/edit', \App\Http\Livewire\Front\Account\AccountForm::class)
            ->middleware('can:edit user')
            ->name('edit');
    });

    Route::prefix('/cost-center')->name('cost_center.')->group(function () {
        Route::get('/list', \App\Http\Livewire\Front\CostCenter\CostCenterDataTable::class)
            ->middleware('can:see cost center')
            ->name('list');
        Route::get('/create', \App\Http\Livewire\Front\CostCenter\CostCenterForm::class)
            ->middleware('can:create cost center')
            ->name('create');
        Route::get('/{center}/edit', \App\Http\Livewire\Front\CostCenter\CostCenterForm::class)
            ->middleware('can:edit cost center')
            ->name('edit');
    });

    Route::prefix('/invoice')->name('invoice.')->group(function () {
        Route::get('/list', \App\Http\Livewire\Front\Invoice\InvoiceDataTable::class)
            ->middleware('can:see facture')
            ->name('list');
        Route::get('/{facture}/show', [FacturationsController::class, 'show'])
            ->middleware('can:see facture')
            ->name('show');
        Route::get('/{invoice}/reservations', \App\Http\Livewire\Front\Invoice\InvoiceReservationDataTable::class)
            ->middleware('can:see facture')
            ->name('reservations');
    });
});

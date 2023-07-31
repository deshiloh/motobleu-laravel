<?php


/*
|--------------------------------------------------------------------------
| FRONT Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\FacturationsController;
use App\Http\Livewire\Front\Account\AccountDataTable;
use App\Http\Livewire\Front\Account\AccountForm;
use App\Http\Livewire\Front\Address\AddressDataTable;
use App\Http\Livewire\Front\Address\AddressForm;
use App\Http\Livewire\Front\CostCenter\CostCenterDataTable;
use App\Http\Livewire\Front\CostCenter\CostCenterForm;
use App\Http\Livewire\Front\DashboardHome;
use App\Http\Livewire\Front\Invoice\InvoiceDataTable;
use App\Http\Livewire\Front\Invoice\InvoiceReservationDataTable;
use App\Http\Livewire\Front\Passager\PassagerDataTable;
use App\Http\Livewire\Front\Passager\PassagerForm;
use App\Http\Livewire\Front\Reservation\ReservationDataTable;
use App\Http\Livewire\Front\Reservation\ReservationForm;

Route::prefix('dashboard')->name('front.')->group(function () {

    Route::prefix('/reservation')->name('reservation.')->group(function () {
        Route::get('/', ReservationDataTable::class)
            ->middleware('can:see reservation')
            ->name('list');
        Route::get('/create', ReservationForm::class)
            ->middleware('can:create reservation')
            ->name('create');
    });

    Route::prefix('/address')->name('address.')->group(function () {
        Route::get('/list', AddressDataTable::class)
            ->middleware('can:see address reservation')
            ->name('list');
        Route::get('/create', AddressForm::class)
            ->middleware('can:create address reservation')
            ->name('create');
        Route::get('/{address}/edit', AddressForm::class)
            ->middleware('can:edit address reservation')
            ->name('edit');
    });

    Route::prefix('/passager')->name('passager.')->group(function () {
        Route::get('/list', PassagerDataTable::class)
            ->middleware('can:see passenger')
            ->name('list');
        Route::get('/create', PassagerForm::class)
            ->middleware('can:create passenger')
            ->name('create');
        Route::get('/{passager}/edit', PassagerForm::class)
            ->middleware('can:edit passenger')
            ->name('edit');
    });

    // DISPONIBLE UNIQUEMENT POUR ARDIAN
    Route::prefix('/secretary')->name('user.')->group(function () {
        Route::get('/list', AccountDataTable::class)
            ->middleware('can:see user')
            ->name('list');
        Route::get('/create', AccountForm::class)
            ->middleware('can:create user')
            ->name('create');
        Route::get('/{account}/edit', AccountForm::class)
            ->middleware('can:edit user')
            ->name('edit');
    });

    Route::prefix('/cost-center')->name('cost_center.')->group(function () {
        Route::get('/list', CostCenterDataTable::class)
            ->middleware('can:see cost center')
            ->name('list');
        Route::get('/create', CostCenterForm::class)
            ->middleware('can:create cost center')
            ->name('create');
        Route::get('/{center}/edit', CostCenterForm::class)
            ->middleware('can:edit cost center')
            ->name('edit');
    });

    Route::prefix('/invoice')->name('invoice.')->group(function () {
        Route::get('/list', InvoiceDataTable::class)
            ->name('list');
        Route::get('/{facture}/show', [FacturationsController::class, 'show'])
            ->name('show');
        Route::get('/{invoice}/reservations', InvoiceReservationDataTable::class)
            ->name('reservations');
    });
});

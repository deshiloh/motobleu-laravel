<?php

namespace App\Providers;

use App\Events\BillCreated;
use App\Events\ReservationCanceled;
use App\Events\ReservationCanceledPay;
use App\Events\ReservationConfirmed;
use App\Listeners\BillCreatedListener;
use App\Listeners\ReservationCanceledListener;
use App\Listeners\ReservationCanceledPayListener;
use App\Listeners\ReservationConfirmedListener;
use App\Listeners\SendEmailNotification;
use App\Models\Reservation;
use App\Models\User;
use App\Observers\AccountObserver;
use App\Observers\ReservationObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        ReservationConfirmed::class => [
            ReservationConfirmedListener::class
        ],

        ReservationCanceled::class => [
            ReservationCanceledListener::class
        ],

        ReservationCanceledPay::class => [
            ReservationCanceledPayListener::class
        ],

        BillCreated::class => [
            BillCreatedListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        User::observe(AccountObserver::class);
        Reservation::observe(ReservationObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

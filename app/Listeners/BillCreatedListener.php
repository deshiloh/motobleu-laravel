<?php

namespace App\Listeners;

use App\Events\BillCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BillCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\BillCreated  $event
     * @return void
     */
    public function handle(BillCreated $event)
    {
        // TODO Send Email
        ray($event->facture);
    }
}

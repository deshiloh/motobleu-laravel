<?php

namespace App\Listeners;

use App\Events\BillCreated;
use App\Mail\BillCreated as MailBillCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

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
        Mail::to('toto@test.com')
            ->send(new MailBillCreated($event->facture));
    }
}

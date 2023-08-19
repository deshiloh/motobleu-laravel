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
     * Handle the event.
     *
     * @param BillCreated $event
     * @return void
     */
    public function handle(BillCreated $event): void
    {
        $recipients = explode(',', $event->emailData['address']);

        foreach ($recipients as $recipient) {
            $recipient = trim($recipient);

            try {
                Mail::to($recipient)
                    ->send(new MailBillCreated($event->facture, $event->emailData['message']));
            }catch (\Exception $exception) {
                continue;
            }
        }
    }
}

<?php

namespace App\Events;

use App\Models\Facture;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Facture $facture;
    public array $emailData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Facture $facture, array $emailData)
    {
        $this->facture = $facture;
        $this->emailData = $emailData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

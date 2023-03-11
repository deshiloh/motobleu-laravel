<?php

namespace Tests\Unit\app\Listeners;

use App\Events\BillCreated;
use App\Listeners\BillCreatedListener;
use App\Models\Facture;
use Tests\TestCase;

class BillCreatedTest extends TestCase
{
    private BillCreatedListener $billCreatedListener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->billCreatedListener = new BillCreatedListener();
    }

    public function testHandleSendEmailTwice()
    {
        \Mail::fake();

        $facture = $this->createMock(Facture::class);

        $event = $this->createMock(BillCreated::class);
        $event->emailData = [
            'address' => 'test@test.com, toto@test.com',
            'message' => 'message'
        ];
        $event->facture = $facture;

        $this->billCreatedListener->handle($event);

        \Mail::assertSent(\App\Mail\BillCreated::class, 2);
    }

    public function testHandle()
    {
        \Mail::fake();

        $facture = $this->createMock(Facture::class);

        $event = $this->createMock(BillCreated::class);
        $event->emailData = [
            'address' => 'test@test.com',
            'message' => 'message'
        ];
        $event->facture = $facture;

        $this->billCreatedListener->handle($event);

        \Mail::assertSent(\App\Mail\BillCreated::class, 1);
    }
}

<?php

namespace Tests\Feature;

use App\Mail\BillCreated;
use App\Mail\ReservationCreated;
use App\Models\Facture;
use App\Models\Reservation;
use App\Services\InvoiceService;
use app\Settings\BillSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Mail\Attachment;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function  testReservationCreatedContent()
    {
        $reservation = Reservation::find(1);

        $mailable = new ReservationCreated($reservation);

        $mailable->assertSeeInHtml($reservation->reference);
    }
}

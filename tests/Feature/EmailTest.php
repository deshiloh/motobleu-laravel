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
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected bool $seed = true;

    /**
     * A basic feature test example.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function testEmailBilledCreateContent(): void
    {
        BillSettings::fake([
            'entreprises_xls_file' => [1],
            'entreprises_cost_center_facturation' => [1],
            'entreprises_command_field' => [1],
        ]);

        $facture = Facture::factory()->create();
        $reservation = Reservation::find(1);
        $reservation->update([
            'facture_id' => $facture->id
        ]);

        $mailable = new BillCreated($facture, 'test');

        $mailable->assertHasSubject("MOTOBLEU / Votre facturation (" . $facture->month . " / " . $facture->year . ")");
    }

    public function  testReservationCreatedContent()
    {
        $reservation = Reservation::find(1);

        $mailable = new ReservationCreated($reservation);

        $mailable->assertSeeInHtml($reservation->reference);
    }
}
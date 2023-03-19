<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\Invoice\InvoiceReservationDataTable;
use App\Models\Facture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $user->entreprises()->attach(1);
        $user->assignRole(['admin', 'super admin']);

        $this->actingAs($user);
    }

    /**
     * @return void
     */
    public function testAccessAddressList(): void
    {
        $response = $this->get(route('front.invoice.list'));

        $response->assertStatus(200);
    }

    public function testAccessReservationsList()
    {
        $response = $this->get(route('front.invoice.reservations', ['invoice' => Facture::find(1)]));
        $response->assertStatus(200);
    }

    public function testRoleUserCanNotAccess()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('front.invoice.list'));
        $response->assertStatus(403);
    }
}

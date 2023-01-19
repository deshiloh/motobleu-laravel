<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\Address\AddressDataTable;
use App\Http\Livewire\Front\Address\AddressForm;
use App\Http\Livewire\Front\Passager\PassagerDataTable;
use App\Http\Livewire\Front\Passager\PassagerForm;
use App\Models\AdresseReservation;
use App\Models\Passager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PassagerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);
    }

    /**
     * @return void
     */
    public function testAccessAddressList(): void
    {
        $response = $this->get(route('front.passager.list'));

        $response->assertStatus(200);
    }

    public function testAccessCreatePage(): void
    {
        $response = $this->get(route('front.passager.create'));
        $response->assertStatus(200);
    }

    public function testCreatePassagerWithErrors(): void
    {
        Livewire::test(PassagerForm::class)
            ->set('passager.nom', '')
            ->set('passager.email', '')
            ->set('passager.telephone', '')
            ->set('passager.portable', '')
            ->set('passager.cost_center_id', '')
            ->set('passager.type_facturation_id', '')
            ->call('save')
            ->assertHasErrors([
                'passager.nom' => 'required',
                'passager.email' => 'required',
                'passager.telephone' => 'required',
                'passager.portable' => 'required'
            ]);
    }

    public function testCreatePassenger(): void
    {
        Livewire::test(PassagerForm::class)
            ->set('passager.nom', 'test')
            ->set('passager.email', 'test@test.com')
            ->set('passager.telephone', 'test')
            ->set('passager.portable', 'test')
            ->set('passager.cost_center_id', '')
            ->set('passager.type_facturation_id', '')
            ->call('save')
            ->assertHasNoErrors();
    }

    public function testConfirmDeletePassengerDialogIsCalled()
    {
        $passager = Passager::factory(['user_id' => 1])->create();
        Livewire::test(PassagerDataTable::class)
            ->call('deletePassenger', $passager)
            ->assertDispatchedBrowserEvent('wireui:confirm-dialog')
        ;
    }

    public function testDeletePassenger()
    {
        /** @var Passager $passager */
        $passager = Passager::factory(['user_id' => 1, 'is_actif' => true])->create();

        Livewire::test(PassagerDataTable::class)
            ->call('confirmDeletePassenger', $passager)
            ->assertDispatchedBrowserEvent('wireui:notification');

        $this->assertTrue($passager->is_actif == false);
    }
}

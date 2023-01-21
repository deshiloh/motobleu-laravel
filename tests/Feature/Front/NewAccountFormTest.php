<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\Address\AddressDataTable;
use App\Http\Livewire\Front\Address\AddressForm;
use App\Http\Livewire\Front\NewAccountForm;
use App\Mail\ConfirmationRegisterUserDemand;
use App\Mail\RegisterUserDemand;
use App\Models\AdresseReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewAccountFormTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * @return void
     */
    public function testAccessAddressList(): void
    {
        $response = $this->get(route('account.new'));

        $response->assertStatus(200);
    }

    public function testSendRegister()
    {
        \Mail::fake();

        Livewire::test(NewAccountForm::class)
            ->set('user.nom', 'test')
            ->set('user.prenom', 'test')
            ->set('user.email', 'test@test.com')
            ->set('user.telephone', 'test')
            ->set('user.adresse', 'test')
            ->set('user.adresse_bis', 'test')
            ->set('user.code_postal', 'test')
            ->set('user.ville', 'test')
            ->set('entrepriseName', 'test')
            ->call('send')
            ->assertHasNoErrors()
            ;

        \Mail::assertSent(RegisterUserDemand::class);
        \Mail::assertSent(ConfirmationRegisterUserDemand::class);
    }

    public function testSendErrors()
    {
        \Mail::fake();

        Livewire::test(NewAccountForm::class)
            ->set('user.nom')
            ->set('user.prenom')
            ->set('user.email')
            ->set('user.telephone')
            ->set('user.adresse')
            ->set('user.adresse_bis')
            ->set('user.code_postal')
            ->set('user.ville')
            ->set('entrepriseName', '')
            ->call('send')
            ->assertHasErrors([
                'user.nom' => 'required',
                'user.prenom' => 'required',
                'user.email' => 'required',
                'user.telephone' => 'required',
                'user.adresse' => 'required',
                'user.code_postal' => 'required',
                'user.ville' => 'required',
                'entrepriseName' => 'required'
            ])
        ;

        \Mail::assertNothingSent();
    }
}

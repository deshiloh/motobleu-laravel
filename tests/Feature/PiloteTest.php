<?php

namespace Tests\Feature;

use App\Http\Livewire\Pilote\PiloteDataTable;
use App\Http\Livewire\Pilote\PiloteForm;
use App\Http\Livewire\Pilote\RecapReservationPilote;
use App\Models\Pilote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PiloteTest extends TestCase
{
    use RefreshDatabase;


    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::find(1);
        $user->assignRole('super admin');

        $this->actingAs($user);
    }

    public function testAcessListPilotes()
    {
        $response = $this->get(route('admin.pilotes.index'));
        $response->assertStatus(200);
    }

    public function testCanAcessCreateFormPilote()
    {
        $response = $this->get(route('admin.pilotes.create'));
        $response->assertStatus(200);
    }

    public function testAccessRecapPiloteReservations()
    {
        $pilote = Pilote::find(1);
        $response = $this->get(route('admin.pilotes.recap-reservation', ['pilote' => $pilote->id]));
        $response->assertStatus(200);
    }

    public function testCreatePiloteWithErrors()
    {
        Livewire::test(PiloteForm::class)
            ->set('pilote.nom', '')
            ->set('pilote.prenom', '')
            ->set('pilote.email', '')
            ->set('pilote.entreprise', '')
            ->set('pilote.adresse', '')
            ->set('pilote.adresse_complement', '')
            ->set('pilote.code_postal', '')
            ->set('pilote.ville', '')
            ->set('pilote.telephone', '')
            ->call('save')
            ->assertHasErrors([
                'pilote.nom' => 'required',
                'pilote.prenom' => 'required',
                'pilote.email' => 'required',
            ]);
    }

    public function testCreatePiloteOK()
    {
        $pilote = Pilote::factory()->make();

        Livewire::test(PiloteForm::class)
            ->set('pilote.nom', $pilote->nom)
            ->set('pilote.prenom', $pilote->prenom)
            ->set('pilote.telephone', $pilote->telephone)
            ->set('pilote.email', $pilote->email)
            ->set('pilote.entreprise', $pilote->enterprise)
            ->set('pilote.adresse', $pilote->adresse)
            ->set('pilote.adresse_complement', $pilote->adresse_complement)
            ->set('pilote.code_postal', $pilote->code_postal)
            ->set('pilote.ville', $pilote->ville)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pilotes', [
            'nom' => $pilote->nom
        ]);
    }

    public function testEditPiloteOK()
    {
        $piloteExist = Pilote::factory()->create();
        $pilote = Pilote::factory()->make();

        Livewire::test(PiloteForm::class, ['pilote' => $piloteExist])
            ->set('pilote.nom', $pilote->nom)
            ->set('pilote.prenom', $pilote->prenom)
            ->set('pilote.telephone', $pilote->telephone)
            ->set('pilote.email', $pilote->email)
            ->set('pilote.entreprise', $pilote->enterprise)
            ->set('pilote.adresse', $pilote->adresse)
            ->set('pilote.adresse_complement', $pilote->adresse_complement)
            ->set('pilote.code_postal', $pilote->code_postal)
            ->set('pilote.ville', $pilote->ville)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pilotes', [
            'nom' => $pilote->nom
        ]);
    }

    public function testDisablePilote()
    {
        $pilote = Pilote::find(1);

        Livewire::test(PiloteDataTable::class)
            ->call('disablePilote', $pilote)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pilotes', [
            'nom' => $pilote->nom,
            'is_actif' => false
        ]);
    }

    public function testEnablePilote()
    {
        $pilote = Pilote::factory([
            'nom' => 'test',
            'is_actif' => false
        ])->create();

        Livewire::test(PiloteDataTable::class)
            ->call('enablePilote', $pilote)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pilotes', [
            'nom' => 'test',
            'is_actif' => true
        ]);
    }

    public function testUpdateReservationPiloteWithEmptyTarif()
    {
        $pilote = Pilote::find(1);
        Livewire::test(RecapReservationPilote::class, ['pilote' => $pilote])
            ->call('editReservation', [
                'tarif' => '',
                'encaisse' => '0',
                'encompte' => '200',
            ])
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;
        $this->assertDatabaseMissing('reservations', [
            'tarif_pilote' => ''
        ]);
    }

    public function testUpdateReservationPiloteSuccessful()
    {
        $pilote = Pilote::find(1);
        Livewire::test(RecapReservationPilote::class, ['pilote' => $pilote])
            ->call('editReservation', [
                'encaisse' => 200,
                'encompte' => 0,
                'comment' => '',
                'reservation' => 1
            ]);

        $this->assertDatabaseHas('reservations', [
            'encaisse_pilote' => 200
        ]);
    }
}

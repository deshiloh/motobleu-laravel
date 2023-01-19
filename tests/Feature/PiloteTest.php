<?php

namespace Tests\Feature;

use App\Http\Livewire\Pilote\PiloteForm;
use App\Models\Pilote;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
                'pilote.telephone' => 'required',
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

    public function testCanDeletePilote()
    {
        $pilote = Pilote::factory()->create();
        $response = $this->delete(route('admin.pilotes.destroy', ['pilote' => $pilote->id]));

        $response->assertStatus(302);
        $this->assertModelMissing($pilote);
    }
}

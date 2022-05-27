<?php

namespace Tests\Feature;

use App\Models\Pilote;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    /**
     * @var Collection|HasFactory|Model|mixed
     */
    private mixed $pilote;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::find(1);

        $this->actingAs($user);

        $this->pilote = Pilote::find(1);
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

    public function testCanCreatePilote()
    {
        $datas = Pilote::factory()->make()->toArray();

        $response = $this->post(route('admin.pilotes.store'), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pilotes', $datas);
    }

    public function testCreatePiloteWithMissDatas()
    {
        $datas = Pilote::factory()->make()->toArray();
        unset($datas['nom']);
        unset($datas['prenom']);
        unset($datas['telephone']);
        $datas['email'] = 'test';

        $response = $this->post(route('admin.pilotes.store'), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nom', 'prenom', 'telephone', 'email']);
        $this->assertDatabaseMissing('pilotes', $datas);
    }

    public function testCanAcessEditPiloteForm()
    {
        $response = $this->get(route('admin.pilotes.edit', ['pilote' => $this->pilote->id]));
        $response->assertStatus(200);
    }

    public function testCanEditUser()
    {
        $datas = Pilote::factory()->make()->toArray();

        $response = $this->put(route('admin.pilotes.update', ['pilote' => $this->pilote->id]), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pilotes', $datas);
    }

    public function testUpdatePiloteWithMissDatas()
    {
        $datas = Pilote::factory()->make()->toArray();
        unset($datas['nom']);
        unset($datas['prenom']);
        unset($datas['telephone']);
        $datas['email'] = 'test';

        $response = $this->put(route('admin.pilotes.update', ['pilote' => $this->pilote->id]), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nom', 'prenom', 'telephone', 'email']);
        $this->assertDatabaseMissing('pilotes', $datas);
    }

    public function testCanDeletePilote()
    {
        $response = $this->delete(route('admin.pilotes.destroy', ['pilote' => $this->pilote->id]));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('pilotes', $this->pilote->toArray());
    }
}

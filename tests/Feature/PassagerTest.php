<?php

namespace Tests\Feature;

use App\Models\Passager;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PassagerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Collection|HasFactory|Model|mixed
     */
    private mixed $passager;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->passager = $user->passagers()->get()->first();
    }

    public function testCanAccessToListPage()
    {
        $response = $this->get(route('admin.passagers.index'));

        $response->assertStatus(200);
    }

    public function testCanAccessCreateFormPassage()
    {
        $response = $this->get(route('admin.passagers.create'));

        $response->assertStatus(200);
    }

    public function testCreateNewPassager()
    {
        $datas = Passager::factory()->make()->toArray();
        $user = User::factory()->create();
        $datas['user_id'] = $user->id;

        $response = $this->post(route('admin.passagers.store'), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('passagers', $datas);
    }

    public function testAcessFormEditPassager()
    {
        $response = $this->get(route('admin.passagers.edit', ['passager' => $this->passager->id]));

        $response->assertStatus(200);
    }

    public function testCanEditPassager()
    {
        $user = User::factory()->create();
        $datas = $user->passagers()->get()->first()->toArray();
        $datas['nom'] = 'test';
        unset($datas['created_at']);
        unset($datas['updated_at']);

        $response = $this->put(route('admin.passagers.update', [
            'passager' => $datas['id']
        ]), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('passagers', $datas);
    }

    public function testCanDeletePassager()
    {
        $response = $this->delete(route('admin.passagers.destroy', ['passager' => $this->passager->id]));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('passagers', $this->passager->toArray());
    }
}

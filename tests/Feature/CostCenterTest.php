<?php

namespace Tests\Feature;

use App\Models\CostCenter;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CostCenterTest extends TestCase
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
    private mixed $costCenter;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::find(1);
        $this->actingAs($user);

        $this->costCenter = CostCenter::find(1);
    }

    public function testAccessListCostCenter()
    {
        $response = $this->get(route('admin.costcenter.index'));
        $response->assertStatus(200);
    }

    public function testCanAcessCreateCostCenterForm()
    {
        $reponse = $this->get(route('admin.costcenter.create'));
        $reponse->assertStatus(200);
    }

    public function testCanCreateCostCenter()
    {
        $costcenter = CostCenter::factory()->make()->toArray();

        $response = $this->post(route('admin.costcenter.store'), $costcenter);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.costcenter.index'));
        $this->assertDatabaseHas('cost_centers', $costcenter);
    }

    public function testCanCreateNonActifCostCenter()
    {
        $costcenter = CostCenter::factory()->nonActif()->make()->toArray();

        $response = $this->post(route('admin.costcenter.store'), $costcenter);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cost_centers', $costcenter);
    }

    public function testCreateCostCenterWithWrongData()
    {
        $costcenter = CostCenter::factory([
            'nom' => ''
        ])->make()->toArray();

        $response = $this->post(route('admin.costcenter.store'), $costcenter);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nom']);
        $this->assertDatabaseMissing('cost_centers', $costcenter);
    }

    public function testCanAcessEditFormCostCenter()
    {
        $reponse = $this->get(route('admin.costcenter.edit', ['costcenter' => $this->costCenter->id]));
        $reponse->assertStatus(200);
    }

    public function testCanEditCostCenter()
    {
        $costcenter = CostCenter::factory()->make()->toArray();

        $response = $this->put(route('admin.costcenter.update', ['costcenter' => $this->costCenter->id]), $costcenter);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.costcenter.edit', ['costcenter' => $this->costCenter->id]));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cost_centers', $costcenter);
    }

    public function testCanEditCostCenterNonActif()
    {
        $costcenter = CostCenter::factory()->nonActif()->make()->toArray();

        $response = $this->put(route('admin.costcenter.update', ['costcenter' => $this->costCenter->id]), $costcenter);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.costcenter.edit', ['costcenter' => $this->costCenter->id]));
        $this->assertDatabaseHas('cost_centers', $costcenter);
    }

    public function testCanDeleteCostCenter()
    {
        $response = $this->delete(route('admin.costcenter.destroy', ['costcenter' => $this->costCenter->id]));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.costcenter.index'));
        $this->assertDatabaseMissing('cost_centers', $this->costCenter->toArray());
    }
}

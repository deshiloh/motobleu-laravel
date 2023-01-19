<?php

namespace Tests\Feature;

use App\Http\Livewire\CostCenter\CostCenterForm;
use App\Models\CostCenter;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CostCenterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected bool $seed = true;

    /**
     * @var Collection|HasFactory|Model|mixed
     */
    private mixed $costCenter;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::find(1);
        $user->assignRole('super admin');

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


    public function testCreateCostCenterWithErrors()
    {
        Livewire::test(CostCenterForm::class)
            ->set('costCenter.nom', '')
            ->set('costCenter.is_actif', true)
            ->call('save')
            ->assertHasErrors(['costCenter.nom' => 'required']);
    }

    public function testCreateCostCenterOK()
    {
        $costCenter = CostCenter::factory()->make();
        Livewire::test(CostCenterForm::class)
            ->set('costCenter.nom', $costCenter->nom)
            ->set('costCenter.is_actif', true)
            ->call('save')
            ->assertHasNoErrors();
        $this->assertTrue(CostCenter::where('nom', $costCenter->nom)->exists());
    }

    public function testEditCostCenterOK()
    {
        $costCenter = CostCenter::find(1);
        Livewire::test(CostCenterForm::class, ['costCenter' => $costCenter])
            ->set('costCenter.nom', 'test')
            ->set('costCenter.is_actif', true)
            ->call('save')
            ->assertHasNoErrors();
        $this->assertTrue(CostCenter::where('nom', 'test')->exists());
    }

    public function testCanDeleteCostCenter()
    {
        $response = $this->delete(route('admin.costcenter.destroy', ['costcenter' => $this->costCenter->id]));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.costcenter.index'));
        $this->assertDatabaseMissing('cost_centers', $this->costCenter->toArray());
    }
}

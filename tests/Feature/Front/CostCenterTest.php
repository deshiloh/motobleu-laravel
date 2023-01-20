<?php

namespace Tests\Feature\Front;

use App\Http\Livewire\Front\CostCenter\CostCenterDataTable;
use App\Http\Livewire\Front\CostCenter\CostCenterForm;
use App\Models\CostCenter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

class CostCenterTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(['admin', 'super admin']);

        $this->actingAs($user);
    }

    public function testAccessListPage()
    {
        $response = $this->get(route('front.cost_center.list'));
        $response->assertStatus(200);
    }

    public function testRoleUserCanNotAccess()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('front.cost_center.list'));
        $response->assertStatus(403);
    }

    public function testRoleUserCanNotAccessEdit()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $costCenter = CostCenter::factory()->create();

        $response = $this->get(route('front.cost_center.edit', ['center' => $costCenter->id]));
        $response->assertStatus(403);
    }

    public function testRoleUserCanNotAccessCreate()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user);

        $response = $this->get(route('front.cost_center.create'));
        $response->assertStatus(403);
    }

    public function testToggleActifCostCenter()
    {
        /** @var CostCenter $costCenter */
        $costCenter = CostCenter::find(1);

        Livewire::test(CostCenterDataTable::class)
            ->call('toggleActifCostCenter', $costCenter)
            ->assertHasNoErrors()
        ;
        $this->assertTrue($costCenter->is_actif == false);
    }

    public function testCreateCostCenterWithErrors()
    {
        Livewire::test(CostCenterForm::class)
            ->set('costCenter.nom', '')
            ->call('save')
            ->assertHasErrors([
                'costCenter.nom' => 'required'
            ])
        ;
    }

    public function testCreateCostCenter()
    {
        Livewire::test(CostCenterForm::class)
            ->set('costCenter.nom', 'test')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;

        $this->assertTrue(CostCenter::where('nom', 'test')->exists());
    }

    public function testEditCostCenter()
    {
        $costCenter = CostCenter::find(1);

        Livewire::test(CostCenterForm::class, ['center' => $costCenter])
            ->set('costCenter.nom', 'test')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;

        $this->assertTrue(CostCenter::where('nom', 'test')->exists());
    }
}

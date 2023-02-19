<?php

namespace Tests\Feature;

use App\Http\Livewire\Passager\PassagerForm;
use App\Http\Livewire\Passager\PassagersDataTable;
use App\Models\Passager;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class PassagerTest extends TestCase
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
    private mixed $passager;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::find(1);
        $user->assignRole('super admin');

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

    public function testCreatePassagerWithErrors()
    {
        Livewire::test(PassagerForm::class)
            ->set('passager.nom', '')
            ->set('passager.email', '')
            ->set('passager.telephone', '')
            ->set('passager.portable', '')
            ->set('passager.user_id', '')
            ->set('passager.cost_center_id', '')
            ->set('passager.type_facturation_id', '')
            ->call('save')
            ->assertHasErrors([
                'passager.nom' => 'required',
                'passager.email' => 'required',
                'passager.telephone' => 'required',
                'passager.portable' => 'required',
                'passager.user_id' => 'required',
            ]);
    }

    public function testCreatePassagerOK()
    {
        $user = User::factory()->create();
        $passager = Passager::factory()->for($user)->create();

        Livewire::test(PassagerForm::class)
            ->set('passager.nom', $passager->nom)
            ->set('passager.email', $passager->email)
            ->set('passager.telephone', $passager->telephone)
            ->set('passager.portable', $passager->portable)
            ->set('passager.user_id', $passager->user_id)
            ->set('passager.cost_center_id', '')
            ->set('passager.type_facturation_id', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(Passager::where('nom', $passager->nom)->exists());
    }

    public function testDisablePassager()
    {
        $passenger = Passager::find(1);

        Livewire::test(PassagersDataTable::class)
            ->call('disablePassenger', $passenger)
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('passagers', [
            'nom' => $passenger->nom,
            'is_actif' => false
        ]);
    }

    public function testEnablePassager()
    {
        $passenger = Passager::factory([
            'nom' => 'test',
            'is_actif' => false,
            'user_id' => 1
        ])->create();

        Livewire::test(PassagersDataTable::class)
            ->call('enablePassenger', $passenger)
            ->assertHasNoErrors()
            ->assertStatus(200);

        $this->assertDatabaseHas('passagers', [
            'nom' => 'test',
            'is_actif' => true
        ]);
    }

    public function testSearchPassager()
    {
        Passager::factory([
            'nom' => 'test',
            'is_actif' => true,
            'user_id' => 1
        ])->create();

        Livewire::test(PassagersDataTable::class)
            ->set('search', 'test')
            ->assertSee(['test', 'Actif'])
            ->assertHasNoErrors()
            ->assertStatus(200);
    }
}

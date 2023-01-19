<?php

namespace Tests\Feature;

use App\Http\Livewire\TypeFacturation\TypeFacturationForm;
use App\Models\Entreprise;
use App\Models\TypeFacturation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TypeFacturationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates whether the default seeder should run before each test.
     *
     * @var bool
     */
    protected $seed = true;

    private $typeFacturation;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('super admin');

        $this->actingAs($user);

        $this->typeFacturation = TypeFacturation::find(1);
    }

    public function testAcessListTypeFacturation()
    {
        $response = $this->get(route('admin.typefacturation.index'));
        $response->assertStatus(200);
    }

    public function testCanAcessCreateFormTypeFacturation()
    {
        $response = $this->get(route('admin.typefacturation.create'));
        $response->assertStatus(200);
    }

    public function testCreateTypeFacturationWithErrors()
    {
        Livewire::test(TypeFacturationForm::class)
            ->set('typeFacturation.nom', '')
            ->call('save')
            ->assertHasErrors(['typeFacturation.nom' => 'required']);
    }

    public function testCreateTypeFacturationOK()
    {
        $typeFacturation = TypeFacturation::find(1);

        Livewire::test(TypeFacturationForm::class, ['typeFacturation' => $typeFacturation])
            ->set('typeFacturation.nom', 'test')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('type_facturations', [
            'nom' => 'test'
        ]);
    }

    public function testDeleteTypeFacturation()
    {
        $response = $this->delete(route('admin.typefacturation.destroy', [
            'typefacturation' => $this->typeFacturation->id
        ]));
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.typefacturation.index'));
        $this->assertDatabaseMissing('type_facturations', $this->typeFacturation->toArray());
    }
}

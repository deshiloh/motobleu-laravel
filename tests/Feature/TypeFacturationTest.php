<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\TypeFacturation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $this->actingAs(User::find(1));

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

    public function testCanCreateTypeFacturation()
    {
        $this->withoutExceptionHandling();
        $datas = TypeFacturation::factory()->make()->toArray();
        $entreprise = Entreprise::factory()->create();
        $datas['entreprise_id'] = $entreprise->id;

        $response = $this->post(route('admin.typefacturation.store'), $datas);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.typefacturation.index'));
        $this->assertDatabaseHas('type_facturations', $datas);
    }

    public function testCanCreateTypeFacturationWithWrongData()
    {
        $datas['nom'] = '';
        $datas['entreprise_id'] = '';

        $response = $this->post(route('admin.typefacturation.store'), $datas);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nom', 'entreprise_id']);
        $this->assertDatabaseMissing('type_facturations', $datas);
    }

    public function testAccessTypeFacturationEditForm()
    {
        $response = $this->get(route('admin.typefacturation.edit', [
            'typefacturation' => $this->typeFacturation->id
        ]));
        $response->assertStatus(200);
    }

    public function testEditTypeFacturation()
    {
        /** @var Entreprise $entreprise */
        $entreprise = Entreprise::find(1);
        $typeFacturation = $entreprise->typeFacturations()->get()->first();

        $datas = $typeFacturation->toArray();
        unset($datas['created_at']);
        unset($datas['updated_at']);

        $response = $this->put(route('admin.typefacturation.update', [
            'typefacturation' => $this->typeFacturation->id
        ]), $datas);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('type_facturations', $datas);
    }

    public function testEditTypeFacturationWithWrongDatas()
    {
        $response = $this->put(route('admin.typefacturation.update', [
            'typefacturation' => $this->typeFacturation->id
        ]), [
            'nom' => '',
            'entreprise_id' => ''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nom', 'entreprise_id']);
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

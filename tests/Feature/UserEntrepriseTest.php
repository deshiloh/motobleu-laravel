<?php

namespace Tests\Feature;

use App\Http\Livewire\Entreprise\UsersEntrepriseDataTable;
use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserEntrepriseTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * @var Entreprise
     */
    private Entreprise $entreprise;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entreprise = Entreprise::find(1);
    }

    public function testSeeUserInPage(): void
    {
        $user = $this->entreprise->users()->get()[0];

        Livewire::test(UsersEntrepriseDataTable::class, ['entreprise' => $this->entreprise])
            ->assertSee($user->nom);
    }

    public function testAddUserEmptyInEntreprise(): void
    {
        Livewire::test(UsersEntrepriseDataTable::class, ['entreprise' => $this->entreprise])
            ->set('userId', '')
            ->call('attach')
            ->assertDispatchedBrowserEvent('wireui:notification');
    }

    public function testAddUserExistInEntreprise(): void
    {
        $user = User::find(1);

        Livewire::test(UsersEntrepriseDataTable::class, ['entreprise' => $this->entreprise])
            ->set('userId', $user)
            ->call('attach')
            ->assertDispatchedBrowserEvent('wireui:notification');
        $this->assertTrue($this->entreprise->users()->where('id', '=', $user->id)->exists());
    }
}

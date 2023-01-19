<?php

namespace Tests\Feature;

use App\Http\Livewire\Account\AccountForm;
use App\Http\Livewire\Account\EditPasswordForm;
use App\Http\Livewire\Pages\PageForm;
use App\Models\Entreprise;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PageTest extends TestCase
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
        $user = User::factory()->create();
        $user->assignRole('super admin');

        $this->actingAs($user);
    }

    public function testAccessPageForm()
    {
        $response = $this->get(route('admin.pages'));
        $response->assertStatus(200);
    }

    public function testPageFormWithErrors()
    {
        Livewire::test(PageForm::class)
            ->set('data.titleFR', '')
            ->set('data.titleEN', '')
            ->set('data.contentFR', '')
            ->set('data.contentEN', '')
            ->call('savePage')
            ->assertHasErrors([
                'data.titleFR' => 'required',
                'data.titleEN' => 'required',
                'data.contentFR' => 'required',
                'data.contentEN' => 'required',
            ])
        ;
    }

    public function testPageCreatedSuccess()
    {
        Livewire::test(PageForm::class)
            ->set('contextNewPage', true)
            ->set('data.titleFR', 'test')
            ->set('data.titleEN', 'test')
            ->set('data.contentFR', 'test')
            ->set('data.contentEN', 'test')
            ->call('savePage')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;

        $this->assertTrue(Page::where('title->fr','test')->exists());
    }

    public function testPageUpdatedSuccess()
    {
        /** @var Page $page */
        $page = Page::factory()->create();

        Livewire::test(PageForm::class)
            ->set('contextNewPage', false)
            ->set('selectedPage', $page)
            ->set('data.titleFR', 'toto')
            ->set('data.titleEN', 'tutu')
            ->set('data.contentFR', 'test fr')
            ->set('data.contentEN', 'test en')
            ->call('savePage')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('wireui:notification')
        ;

        $this->assertTrue(Page::where('title->en','tutu')->exists());
    }
}

<?php

namespace Tests\Feature\Front;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * @return void
     */
    public function testAccessFrontPage()
    {
        /** @var Page $page */
        $page = Page::factory()->create();

        $response = $this->get(route('pages', ['slug' => $page->slug]));
        $response->assertStatus(200);
    }
}

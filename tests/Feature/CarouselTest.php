<?php

namespace Tests\Feature;

use App\Http\Livewire\Carousel\CarouselDataTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class CarouselTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::find(1));
    }

    public function testAccessCarouselPage(): void
    {
        $response = $this->get(route('admin.carousel'));
        $response->assertStatus(200);
    }

    public function testAddCarouselWithErrors(): void
    {
        Livewire::test(CarouselDataTable::class)
            ->call('saveImage')
            ->assertHasErrors([
                'photo' => 'image',
                'position' => 'required'
            ]);
    }

    public function testAddCarouselSuccess()
    {
        \Storage::fake('photos');

        $file = UploadedFile::fake()->image('test.png');

        Livewire::test(CarouselDataTable::class)
            ->set('photo', $file)
            ->set('position', 1)
            ->call('saveImage')
            ->assertHasNoErrors()
        ;

        $this->assertDatabaseHas('carousels', [
            'position' => 1
        ]);
    }

    public function testUploadFile()
    {
        \Storage::fake('photos');

        $file = UploadedFile::fake()->image('test.png');
        $name = $file->hashName();

        Livewire::test(CarouselDataTable::class)
            ->set('photo', $file)
            ->call('upload', $name)
        ;

        \Storage::disk('photos')->assertExists($name);
    }
}

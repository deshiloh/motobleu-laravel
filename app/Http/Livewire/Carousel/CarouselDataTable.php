<?php

namespace App\Http\Livewire\Carousel;

use App\Models\Carousel;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use WireUi\Traits\Actions;

class CarouselDataTable extends Component
{
    use WithFileUploads, Actions;

    public $photo;
    public ?int $position;

    protected array $rules = [
        'photo' => 'image|required',
        'position' => 'required'
    ];

    public function mount()
    {
        $this->position = null;
    }

    public function render()
    {
        return view('livewire.carousel.carousel-data-table', [
            'photosTop' => Carousel::where('position', 1)->paginate(10),
            'photosMiddle' => Carousel::where('position', 2)->paginate(10),
            'photosBottom' => Carousel::where('position', 3)->paginate(10),
        ])
            ->layout('components.layout');
    }

    public function saveImage()
    {
        $this->validate();

        ray($this->photo->hashName());

        try {
            Carousel::create([
                'file_name' => $this->photo->hashName(),
                'position' => $this->position
            ]);

            $this->photo->store('photos');

            $this->notification()->success(
                title: "Opération réussite.",
                description: "La photo a bien été ajoutée."
            );

            $this->photo = null;
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur est survenue",
                description: "Une erreur est survenue pendant le traitement."
            );
            ray()->exception($exception);
        }
    }

    public function deleteCarousel(Carousel $carousel)
    {

    }
}

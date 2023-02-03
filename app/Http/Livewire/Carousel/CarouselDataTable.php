<?php

namespace App\Http\Livewire\Carousel;

use App\Models\Carousel;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use WireUi\Traits\Actions;

class CarouselDataTable extends Component
{
    use WithFileUploads, Actions;

    /**
     * @var TemporaryUploadedFile
     */
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

        $fileName = $this->photo->hashName();

        try {
            Carousel::create([
                'file_name' => $fileName,
                'position' => $this->position
            ]);

            $this->upload($fileName);

            $this->notification()->success(
                title: "Opération réussite.",
                description: "La photo a bien été ajoutée."
            );

            $this->resetFields();
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur est survenue",
                description: "Une erreur est survenue pendant le traitement."
            );
            if (App::environment(['local'])) {
                ray()->exception($exception);
            }
            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant l'enregistrement de la photo carousel", [
                    'exception' => $exception,
                    'photo' => $this->photo
                ]);
            }
        }
    }

    public function resetFields()
    {
        $this->photo = null;
        $this->position = null;
    }

    public function upload(string $name)
    {
        $this->photo->storeAs('/', $name, $disk = 'photos');
    }

    public function deleteCarousel(Carousel $carousel)
    {
        $storage = \Storage::disk('photos');

        try {
            if ($storage->fileExists($carousel->file_name)) {
                $storage->delete($carousel->file_name);
                $carousel->delete();
                $this->notification()->success(
                    title: "Opération réussite.",
                    description: "La photo a bien été supprimée."
                );
            }
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Erreur",
                description: "Une erreur est survenue, veuillez réessayer ultérieurement."
            );
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['prod', 'beta'])) {
                \Log::channel("sentry")->error("Erreur pendant la suppression d'un Slide Carousel", [
                    'exception' => $exception,
                    'carousel' => $carousel
                ]);
            }
        }
    }
}

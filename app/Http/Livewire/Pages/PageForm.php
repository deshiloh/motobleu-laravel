<?php

namespace App\Http\Livewire\Pages;

use App\Models\Page;
use Livewire\Component;
use WireUi\Traits\Actions;

class PageForm extends Component
{
    use Actions;

    public bool $contextNewPage = false;
    public bool $editPageModal = false;
    public ?Page $selectedPage;
    public array $data = [];
    protected array $rules = [
        'data.titleFR' => 'required',
        'data.titleEN' => 'required',
        'data.contentFR' => 'required',
        'data.contentEN' => 'required',
    ];

    public function mount()
    {
        $this->selectedPage = null;
    }

    public function render()
    {
        return view('livewire.pages.page-form')
            ->layout('components.layout');
    }

    public function selectedPage(Page $page)
    {
        $this->selectedPage = $page;
        $this->updateData();
        $this->editPageModal = true;
        $this->contextNewPage = false;
    }

    public function updateData()
    {
        $this->data = [
            'titleFR' => $this->selectedPage->getTranslation('title', 'fr'),
            'titleEN' => $this->selectedPage->getTranslation('title', 'en'),
            'contentFR' => $this->selectedPage->getTranslation('content', 'fr'),
            'contentEN' => $this->selectedPage->getTranslation('content', 'en'),
        ];
    }

    public function showNewPageForm()
    {
        $this->selectedPage = new Page();
        $this->updateData();
        $this->editPageModal = true;
        $this->contextNewPage = true;
    }

    public function savePage()
    {
        $this->validate();

        try {
            if ($this->contextNewPage) {
                Page::create([
                    'title' => [
                        'fr' => $this->data['titleFR'],
                        'en' => $this->data['titleEN']
                    ],
                    'content' => [
                        'fr' => $this->data['contentFR'],
                        'en' => $this->data['contentEN']
                    ]
                ]);
            } else {
                $this->selectedPage->update([
                    'title' => [
                        'fr' => $this->data['titleFR'],
                        'en' => $this->data['titleEN']
                    ],
                    'content' => [
                        'fr' => $this->data['contentFR'],
                        'en' => $this->data['contentEN']
                    ]
                ]);
            }

            $this->notification()->success(
                title: "Opération réussite",
                description: $this->contextNewPage ? "La page a bien été créée." : "La page a bien été modifiée."
            );
        } catch (\Exception $exception) {
            $this->notification()->error(
                title: "Une erreur est survenue", description: "Erreur pendant la création de la page."
            );

            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }
            // TODO SENTRY
        }
    }
}

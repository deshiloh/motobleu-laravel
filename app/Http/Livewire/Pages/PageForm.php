<?php

namespace App\Http\Livewire\Pages;

use App\Models\Page;
use Livewire\Component;

class PageForm extends Component
{
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
    }

    public function savePage()
    {
        $this->validate();
    }
}

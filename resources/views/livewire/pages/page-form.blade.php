<div>
    <x-header>
        Pages
        <x-slot:right>
            <x-button icon="plus" label="Ajouter une page" primary wire:click="showNewPageForm"/>
        </x-slot:right>
    </x-header>
    <x-bloc-content>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>Titre</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse(\App\Models\Page::all() as $page)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $page->title }}</x-datatable.td>
                        <x-datatable.td>
                            <div class="space-x-2">
                                <x-button.circle primary icon="eye" wire:click="selectedPage({{ $page }})"/>
                                <x-button.circle red icon="trash" />
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                    @empty
                    <x-datatable.tr>
                        <x-datatable.td>
                            <div class="text-center">
                                Aucune page
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </x-bloc-content>
    <x-modal.card title="Formulaire Page" blur wire:model.defer="editPageModal">
        <x-errors />
        @if($selectedPage)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="Titre FR" placeholder="Votre titre" wire:model.defer="data.titleFR"/>
                <x-input label="Titre EN" placeholder="Votre titre" wire:model.defer="data.titleEN"/>
            </div>
            <div class="space-y-2 mt-2">
                <x-tinymce wire:model="data.contentFR" label="Contenu FR"/>
                <x-tinymce wire:model="data.contentEN" label="Content EN"/>
            </div>
        @endif
        <x-slot name="footer">
            <div class="flex justify-end">
                <x-button flat label="Annuler" x-on:click="close" />
                <x-button primary label="Enregistrer" wire:click="savePage" />
            </div>
        </x-slot>
    </x-modal.card>
</div>

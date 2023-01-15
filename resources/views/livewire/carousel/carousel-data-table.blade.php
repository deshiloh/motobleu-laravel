<div>
    <x-header>
        Photos de la page d'accueil
    </x-header>
    <x-bloc-content>
        <x-errors class="mb-5"/>
        <form wire:submit.prevent="saveImage" x-data="" enctype="multipart/form-data">
            <div class="grid grid-cols-3 gap-5">
                <div class="w-full h-64 border rounded-lg">
                    @if($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="" class="object-cover w-full h-full rounded-lg">
                    @endif
                </div>
                <div>
                    <div class="space-y-3">
                        <x-button x-data="" x-on:click="$refs.fileinput.click()" label="Choisir une photo" gray />
                        <x-select label="Position" wire:model="position" placeholder="Choisir l'emplacement">
                            <x-select.option label="Haut" value="1" />
                            <x-select.option label="Milieu" value="2" />
                            <x-select.option label="Bas" value="3" />
                        </x-select>
                    </div>
                </div>
            </div>
            <input type="file" x-ref="fileinput" class="hidden" wire:model="photo">
            <div class="mt-2">
                <x-button type="submit" primary label="Enregistrer"/>
            </div>
        </form>
    </x-bloc-content>
    <x-bloc-content>
        <div class="text-xl font-bold">Photos en haut</div>
    </x-bloc-content>
    <x-bloc-content>
        <div class="text-xl font-bold">Photos au milieu</div>
    </x-bloc-content>
    <x-bloc-content>
        <div class="text-xl font-bold mb-5">Photos en bas</div>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>Photo</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @foreach($photosBottom as $photo)
                    <x-datatable.tr>
                        <x-datatable.td>
                            <img src="{{ asset('photos/'.$photo->file_name) }}" alt="" class="h-64 rounded-lg">
                        </x-datatable.td>
                        <x-datatable.td>
                            <x-button.circle icon="trash" red lg />
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforeach
            </x-slot:body>
        </x-datatable>
    </x-bloc-content>
</div>

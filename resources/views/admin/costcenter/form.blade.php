<x-admin-layout>
    <x-title-section>
        <x-slot:title>
            @isset($costcenter)
                Gestion du Cost Center <span class="text-blue-700">{{ $costcenter->nom }}</span>
            @else
                Création d'un nouveau Cost Center
            @endisset
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <x-form method="post"
                route="{{ isset($costcenter) ? route('admin.costcenter.update', ['costcenter' => $costcenter->id]) : route('admin.costcenter.store') }}" :put="isset($costcenter)">
            <x-form.input type="text" label="Nom" name="nom" required="true"
                          :value="isset($costcenter) ? $costcenter->nom : ''"></x-form.input>
            <x-form.toggle value="1" :is-checked="isset($costcenter) ? $costcenter->is_actif : true" name="is_actif">
                Actif
            </x-form.toggle>
        </x-form>
    </x-admin.content>
</x-admin-layout>

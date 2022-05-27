<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            @isset($localisation)
                test
            @else
                Création d'une nouvelle localisation
            @endisset
        </x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form method="post"
                route="{{ isset($localisation) ? route('admin.localisations.update', ['localisation' => $localisation->id]) : route('admin.localisations.store') }}"
                :put="isset($localisation)">
            <x-form.input type="text" label="Nom" name="nom" required="true"
                          value="{{ isset($localisation) ? $localisation->nom : '' }}"></x-form.input>
            <x-form.input type="text" label="Adresse" name="adresse" required="true"
                          value="{{ isset($localisation) ? $localisation->adresse : '' }}"></x-form.input>
            <x-form.input type="text" label="Adresse complémentaire" name="adresse_complement"
                          value="{{ isset($localisation) ? $localisation->adresse_complement : '' }}"></x-form.input>
            <x-form.input type="text" label="Code postal" name="code_postal" required="true"
                          value="{{ isset($localisation) ? $localisation->code_postal : '' }}"></x-form.input>
            <x-form.input type="text" label="Ville" name="ville" required="true"
                          value="{{ isset($localisation) ? $localisation->ville : '' }}"></x-form.input>
            <x-form.input type="text" label="Téléphone" name="telephone" required="true"
                          value="{{ isset($localisation) ? $localisation->telephone : '' }}"></x-form.input>
            <x-form.toggle name="is_actif" value="1" :is-checked="isset($localisation) ? $localisation->is_actif : true" >
                Actif
            </x-form.toggle>
        </x-form>
    </x-admin.content>
</x-admin-layout>

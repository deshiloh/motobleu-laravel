<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            @isset($pilote)
                Gestion de la fiche pilote <span class="text-blue-700">{{ $pilote->nom }} {{ $pilote->prenom }}</span>
            @else
                Création d'un passager
            @endisset
        </x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form method="post"
                route="{{ isset($pilote) ? route('admin.pilotes.update', ['pilote' => $pilote]) : route('admin.pilotes.store') }}"
                :put="isset($pilote)">
            <x-form.input type="text" label="Nom" name="nom" required="true"
                          :value="isset($pilote) ? $pilote->nom : ''"/>
            <x-form.input type="text" label="Prénom" name="prenom" required="true"
                          :value="isset($pilote) ? $pilote->prenom : ''"/>
            <x-form.input type="text" label="Téléphone" name="telephone" required="true"
                          :value="isset($pilote) ? $pilote->telephone : ''"/>
            <x-form.input type="email" label="Email" name="email" required="true"
                          :value="isset($pilote) ? $pilote->email : ''"/>
            <x-form.input type="text" label="Nom de l'entreprise" name="entreprise"
                          :value="isset($pilote) ? $pilote->entreprise : ''"/>
            <x-form.input type="text" label="Adresse" name="adresse"
                          :value="isset($pilote) ? $pilote->adresse : ''"/>
            <x-form.input type="text" label="Adresse complémentaire" name="adresse_complement"
                          :value="isset($pilote) ? $pilote->adresse_complement : ''"/>
            <x-form.input type="text" label="Code postal" name="code_postal"
                          :value="isset($pilote) ? $pilote->code_postal : ''"/>
            <x-form.input type="text" label="Ville" name="ville"
                          :value="isset($pilote) ? $pilote->ville : ''"/>
        </x-form>
    </x-admin.content>
</x-admin-layout>

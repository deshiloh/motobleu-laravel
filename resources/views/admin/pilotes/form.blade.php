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
            <div class="form-group">
                <x-input label="Nom" name="nom" required="required" :value="isset($pilote) ? $pilote->nom : ''" />
            </div>
            <div class="form-group">
                <x-input label="Prénom" name="prenom" required="required" :value="isset($pilote) ? $pilote->prenom : ''" />
            </div>
            <div class="form-group">
                <x-input type="email" label="Adresse email" name="email" required="required" :value="isset($pilote) ? $pilote->email : ''" />
            </div>
            <div class="form-group">
                <x-input label="Téléphone" name="telephone" required="required" :value="isset($pilote) ? $pilote->telephone : ''" />
            </div>
            <div class="form-group">
                <x-input label="Entreprise" name="entreprise" required="required" :value="isset($pilote) ? $pilote->entreprise : ''" />
            </div>
            <div class="form-group">
                <x-input label="Adresse" name="adresse" required="required" :value="isset($pilote) ? $pilote->adresse : ''" />
            </div>
            <div class="form-group">
                <x-input label="Adresse complémentaire" name="adresse_complement" required="required" :value="isset($pilote) ? $pilote->adresse_complement : ''" />
            </div>
            <div class="form-group">
                <x-input label="Code postal" name="code_postal" required="required" :value="isset($pilote) ? $pilote->code_postal : ''" />
            </div>
            <div class="from-group">
                <x-input label="Ville" name="ville" required="required" :value="isset($pilote) ? $pilote->ville : ''" />
            </div>
        </x-form>
    </x-admin.content>
</x-admin-layout>

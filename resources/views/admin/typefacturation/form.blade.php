<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            @isset($typefacturation)
                Gestion du type de facturation <span class="text-blue-700">{{ $typefacturation->nom }}</span>
            @else
                Créer un type de facturation
            @endisset
        </x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form method="post"
                route="{{ isset($typefacturation) ? route('admin.typefacturation.update', ['typefacturation' => $typefacturation]) : route('admin.typefacturation.store') }}"
                :put="isset($typefacturation)">
            <x-form.input type="text" label="Nom" name="nom" required="true"
                          :value="isset($typefacturation) ? $typefacturation->nom : ''"/>
            <x-form.select class="js-example-basic-single" :datas="$entreprises" label="Entreprise" name="entreprise_id" :selected="isset($typefacturation) ? $typefacturation->entreprise_id : false"/>
        </x-form>
    </x-admin.content>
</x-admin-layout>

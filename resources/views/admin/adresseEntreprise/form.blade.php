@php
    $typeSelect = [
        \App\Enum\AdresseEntrepriseTypeEnum::FACTURATION->name => \App\Enum\AdresseEntrepriseTypeEnum::FACTURATION->value,
        \App\Enum\AdresseEntrepriseTypeEnum::PHYSIQUE->name => \App\Enum\AdresseEntrepriseTypeEnum::PHYSIQUE->value,
    ];

    $formRoute = isset($adresse) ?
    route('admin.entreprises.adresses.update', ['entreprise' => $entreprise, 'adress' => $adresse->id]) :
    route('admin.entreprises.adresses.store', ['entreprise' => $entreprise->id]);
@endphp
<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            @isset($adresse)
                Gestion de l'adresse de
                <span class="text-blue-700">{{ \Illuminate\Support\Str::lower($adresse->type_name) }}</span>
                de l'entreprise <span class="text-blue-700">{{ $entreprise->nom }}</span>
            @else
                Création d'une adresse
                pour l'entreprise <span class="text-blue-700">{{ $entreprise->nom }}</span>
            @endisset
        </x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form route="{{ $formRoute }}" method="post" :put="isset($adresse)">
            @isset($adresse)
            @else
                <x-form.select name="type" label="Type" :datas="$typeSelect" :selected="isset($adresse) ? $adresse->type->value : old('type')"/>
            @endisset
            <x-form.input type="text" name="email" value="{{ isset($adresse) ? $adresse->email : '' }}" label="Email de contact"/>
            <x-form.input type="text" name="nom" value="{{ isset($adresse) ? $adresse->nom : '' }}" label="Nom"/>
            <x-form.input type="text" name="adresse" value="{{ isset($adresse) ? $adresse->adresse : '' }}" label="Adresse"/>
            <x-form.input type="text" name="adresse_complement" value="{{ isset($adresse) ? $adresse->adresse_complement : '' }}" label="Adresse complémentaire"/>
            <x-form.input type="text" name="code_postal" value="{{ isset($adresse) ? $adresse->code_postal : '' }}" label="Code postal"/>
            <x-form.input type="text" name="ville" value="{{ isset($adresse) ? $adresse->ville : '' }}" label="Ville"/>
            <x-form.input type="text" name="tva" value="{{ isset($adresse) ? $adresse->tva : '' }}" label="TVA"/>
        </x-form>
    </x-admin.content>
</x-admin-layout>

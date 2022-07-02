@php
    $formRoute = isset($adresse) ?
    route('admin.adresse-reservation.update', ['adresse_reservation' => $adresse->id]) :
    route('admin.adresse-reservation.store');
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
                de réservation
            @endisset
        </x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form route="{{ $formRoute }}" method="post" :put="isset($adresse)">
            <x-form.input type="text" name="adresse" value="{{ isset($adresse) ? $adresse->adresse : '' }}"
                          label="Adresse"/>
            <x-form.input type="text" name="adresse_complement"
                          value="{{ isset($adresse) ? $adresse->adresse_complement : '' }}"
                          label="Adresse complémentaire"/>
            <x-form.input type="text" name="code_postal" value="{{ isset($adresse) ? $adresse->code_postal : '' }}"
                          label="Code postal"/>
            <x-form.input type="text" name="ville" value="{{ isset($adresse) ? $adresse->ville : '' }}" label="Ville"/>
            <x-form.select class="js-example-basic-single" name="user_id" label="Secrétaire" :datas="$userDatas"
                           :selected="isset($adresse) ? $adresse->type->value : old('type')"/>
        </x-form>
    </x-admin.content>
</x-admin-layout>

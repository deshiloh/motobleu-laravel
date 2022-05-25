<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            @isset($passager)
                Gestion de la fiche passager <span class="text-blue-700">{{ $passager->nom }}</span>
                @else
                Création d'un passager
            @endisset
        </x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form method="post" route="{{ isset($passager) ? route('admin.passagers.update', ['passager' => $passager]) : route('admin.passagers.store') }}" :put="isset($passager)">
            <x-form.input type="text" label="Nom & Prénom" name="nom" required="true" :value="isset($passager) ? $passager->nom : ''"/>
            <x-form.input type="text" label="Téléphone bureau" name="telephone" required="true" :value="isset($passager) ? $passager->telephone : ''" />
            <x-form.input type="text" label="Téléphone portable" name="portable" required="true" :value="isset($passager) ? $passager->portable : ''"/>
            <x-form.input type="email" label="Email" name="email" required="true" :value="isset($passager) ? $passager->email : ''"/>
            <x-form.select class="js-example-basic-single" :datas="$users" label="Secrétaire" name="user_id" :selected="isset($passager) ? $passager->user->id : false"/>
        </x-form>
    </x-admin.content>
</x-admin-layout>

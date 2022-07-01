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
            <div class="form-group">
                <x-input label="Nom & prénom" name="nom" required="required" :value="isset($passager) ? $passager->nom : ''" />
            </div>
            <div class="form-group">
                <x-input label="Adresse email" name="email" required="required" :value="isset($passager) ? $passager->email : ''" />
            </div>
            <div class="form-group">
                <x-input label="Téléphone bureau" name="telephone" required="required" :value="isset($passager) ? $passager->telephone : ''" />
            </div>
            <div class="form-group">
                <x-input label="Téléphone portable" name="portable" required="required" :value="isset($passager) ? $passager->portable : ''" />
            </div>
            <div class="form-group">
                <x-input label="Nom & prénom" name="nom" required="required" :value="isset($passager) ? $passager->nom : ''" />
            </div>
            <x-form.select class="js-example-basic-single" :datas="$users" label="Secrétaire" name="user_id" :selected="isset($passager) ? $passager->user->id : false"/>
        </x-form>
    </x-admin.content>
</x-admin-layout>

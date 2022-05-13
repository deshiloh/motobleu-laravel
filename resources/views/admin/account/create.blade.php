@php
    $selectData = [
        'Oui' => 1,
        'Non' => 0
    ];
    $isArdian = $account ? $account->is_admin_ardian : false;
    $isActif = $account ? $account->is_actif : false;
@endphp
<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            {{ $account ? "Modification de l'utilisateur ".$account->nom  : "Création d'un utilisateur"}}
        </x-slot>
    </x-title-section>
    <div class="bg-white dark:bg-gray-900 mt-3 shadow-sm p-3 rounded-lg">
        <x-form put="{{ $account != null }}" method="post"
                route="{{ $account ? route('admin.accounts.update', ['account' => $account]) : route('admin.accounts.store') }}">
            <x-form.input type="text" label="Nom" name="nom" required="true"
                          value="{{ $account ? $account->nom : false }}"></x-form.input>
            <x-form.input type="text" label="Prénom" name="prenom" required="true"
                          value="{{ $account ? $account->prenom : false }}"></x-form.input>
            <x-form.input type="email" label="Email" name="email" required="true"
                          value="{{ $account ? $account->email : false }}"></x-form.input>
            <x-form.input type="tel" label="Téléphone" name="telephone" required="true"
                          value="{{ $account ? $account->telephone : false }}"></x-form.input>
            <x-form.input type="text" label="Adresse" name="adresse" required="true"
                          value="{{ $account ? $account->adresse : false }}"></x-form.input>
            <x-form.input type="text" label="Adresse Bis" name="adresse_bis" required="true"
                          value="{{ $account ? $account->adresse_bis : false }}"></x-form.input>
            <x-form.input type="text" label="Code postal" name="code_postal" required="true"
                          value="{{ $account ? $account->code_postal : false }}"></x-form.input>
            <x-form.select label="Acompte admin Ardian ?" :datas="$selectData"
                           selected="{{ $isArdian }}" name="is_admin_ardian"></x-form.select>
            <x-form.select label="Utilisateur actif ?" :datas="$selectData" selected="{{ $isActif }}"
                           name="is_actif"></x-form.select>
        </x-form>
    </div>
</x-admin-layout>

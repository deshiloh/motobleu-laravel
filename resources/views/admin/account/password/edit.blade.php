<x-admin-layout>
    <x-title-section>
        <x-slot name="title">Modification du mot de passe de {{ $account->prenom }}</x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form
            method="post"
            put="true"
            route="{{ route('admin.accounts.password.update', ['account' => $account->id]) }}"
        >
            <x-form.input type="password" name="password" label="Mot de passe" required="true"></x-form.input>
        </x-form>
    </x-admin.content>
</x-admin-layout>

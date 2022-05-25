<x-admin-layout>
    <x-title-section>
        <x-slot name="title">Création d'une nouvelle entreprise</x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form method="post" route="{{ route('admin.entreprises.store') }}">
            <x-form.input type="text" label="Nom" name="nom" required="true"></x-form.input>
        </x-form>
    </x-admin.content>
</x-admin-layout>

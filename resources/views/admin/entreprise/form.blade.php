<x-admin-layout>
    <x-title-section>
        <x-slot name="title">Création d'une nouvelle entreprise</x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form method="post" route="{{ route('admin.entreprises.store') }}">
            <x-input label="Nom" name="nom" required="required" />
        </x-form>
    </x-admin.content>
</x-admin-layout>

<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Modification de l'entreprise <span class="text-blue-700">{{ $entreprise->nom }}</span>
        </x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form method="post" route="{{ route('admin.entreprises.update', ['entreprise' => $entreprise]) }}" put="true">
            <x-input label="Nom" name="nom" value="{{ $entreprise->nom }}" required="required" />
        </x-form>
    </x-admin.content>
</x-admin-layout>

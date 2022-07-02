<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            @isset($localisation)
                test
            @else
                Création d'une nouvelle localisation
            @endisset
        </x-slot>
    </x-title-section>
    <x-admin.content>
        <x-form method="post"
                route="{{ isset($localisation) ? route('admin.localisations.update', ['localisation' => $localisation->id]) : route('admin.localisations.store') }}"
                :put="isset($localisation)">

        </x-form>
    </x-admin.content>
</x-admin-layout>

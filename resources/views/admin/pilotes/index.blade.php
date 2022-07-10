<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            Liste des pilotes
        </x-slot>
        <a href="{{ route('admin.pilotes.create') }}" class="btn btn-sm btn-primary">
            Créer un pilote
        </a>
    </x-title-section>
    <livewire:pilote.pilote-data-table />
</x-admin-layout>

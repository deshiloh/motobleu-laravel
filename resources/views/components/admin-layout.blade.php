@props([
    'title' => 'Titre'
])
<x-layout>
    <x-alerts></x-alerts>
    <div class="container dark:text-white mx-auto rounded-md mt-3">
        {{ $slot }}
    </div>
</x-layout>

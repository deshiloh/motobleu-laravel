@props([
    'link' => '#'
])
<a href="{{ $link }}" class="bg-blue-700 text-white rounded-md px-2 py-2 text-base hover:bg-blue-800 focus:bg-blue-600">
    {{ $slot }}
</a>

@props([
    'link' => '#'
])
<a class="mr-5 hover:text-gray-400" href="{{ $link }}">{{ $slot }}</a>

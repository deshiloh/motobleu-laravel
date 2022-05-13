@props([
    'link' => '#'
])
<a class="mr-5 hover:text-gray-900" href="{{ $link }}">{{ $slot }}</a>

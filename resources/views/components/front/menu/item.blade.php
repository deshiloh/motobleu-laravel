@props([
    'active' => false
])

<a {{ $attributes->except(['class']) }}
   @class([
        'block max-sm:py-3 sm:inline-flex items-center sm:border-b-2 px-1 pt-1 text-sm font-medium flex space-x-2 transition transition-all',
        'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-300' => !$active,
        'max-sm:bg-white max-sm:text-motobleu border-white-500 text-white' => $active
    ])
>
    {{ $icon }}

    @if($slot->isNotEmpty())
        <span>{{ $slot }}</span>
    @endif
</a>

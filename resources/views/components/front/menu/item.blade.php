@props([
    'active' => false
])

<!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->

<a {{ $attributes->except(['class']) }}
   @class([
        'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium flex space-x-2 transition transition-all',
        'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => !$active,
        'border-white-500 text-white' => $active
    ])
>
    {{ $icon }}

    <span>{{ $slot }}</span>
</a>

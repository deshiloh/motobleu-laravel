@props([
    'active' => false
])
<!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
<a {{ $attributes->class([
        'px-3 py-2 rounded-md text-sm font-medium',
        'text-gray-300 hover:bg-gray-700 hover:text-white ' => !$active,
        'bg-gray-900 text-white' => $active
    ]) }}
>
    {{ $slot }}
</a>

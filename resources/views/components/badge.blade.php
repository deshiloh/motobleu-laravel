@props([
    'success' => false,
    'error' => false
])
<span
    @class([
        'inline-flex items-center rounded-md text-sm font-medium px-2.5 py-0.5',
        'bg-red-100 text-red-800' => $error,
        'bg-green-100 text-green-800' => $success
    ])
>
    {{ $slot }}
</span>

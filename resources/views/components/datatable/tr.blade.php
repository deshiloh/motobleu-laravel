@props([
    'success' => false
])
<tr @class([
        'bg-success text-white' => $success,
        'even:bg-base-300' => !$success
]) {{ $attributes->whereDoesntStartWith('class') }} >
    {{ $slot }}
</tr>

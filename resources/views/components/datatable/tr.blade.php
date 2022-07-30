@props([
    'success' => false
])
<tr @class([
        'bg-success text-white' => $success,
        'even:bg-gray-50 even:dark:bg-slate-700 dark:bg-slate-800' => !$success
]) {{ $attributes->whereDoesntStartWith('class') }} >
    {{ $slot }}
</tr>

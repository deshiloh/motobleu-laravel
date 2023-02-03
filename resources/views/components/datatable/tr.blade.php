@props([
    'success' => false,
    'danger' => false
])
<tr @class([
        'bg-green-100 text-white' => $success,
        'bg-red-100' => $danger,
        'even:bg-gray-50 even:dark:bg-slate-700 dark:bg-slate-800' => !$success && !$danger
]) {{ $attributes->whereDoesntStartWith('class') }} >
    {{ $slot }}
</tr>

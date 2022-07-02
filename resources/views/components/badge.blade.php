@props([
    'success' => true
])
<span @class([
    'text-xs font-semibold mr-2 px-2.5 py-0.5 rounded',
    'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900' => $success,
    'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900' => !$success,
])>
    {{ $success ? 'Oui' : 'Non' }}
</span>



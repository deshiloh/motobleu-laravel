@props([
    'success' => true
])
<span @class([
    'rounded-full text-white px-3 py-1 text-xs uppercase font-medium',
    'bg-green-500' => $success,
    'bg-red-500' => !$success,
])>
    {{ $success ? 'Oui' : 'Non' }}
</span>

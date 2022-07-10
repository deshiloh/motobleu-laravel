@props([
    'success' => true
])
<span @class([
    'badge badge-sm',
    'badge-success' => $success,
    'badge-error' => !$success,
])>
    {{ $success ? 'Oui' : 'Non' }}
</span>

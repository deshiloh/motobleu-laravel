<label {{ $attributes->class([
        'block text-sm font-medium',
        'text-error'  => $hasError,
        'opacity-60'         => $attributes->get('disabled'),
        'text-base-content' => !$hasError,
    ]) }}>
    {{ $label ?? $slot }}
</label>

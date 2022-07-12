<label {{ $attributes->class([
        'label',
        'text-negative-600'  => $hasError,
        'opacity-60'         => $attributes->get('disabled'),
    ]) }}>
    {{ $label ?? $slot }}
</label>

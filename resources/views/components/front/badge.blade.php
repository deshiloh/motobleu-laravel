@props([
    'success'   => false,
    'danger'    => false,
    'warning'   => false,
    'warningSecondary' => false
])

<span
    @class([
        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
        'bg-red-100 text-red-800' => $danger,
        'bg-green-100 text-green-800' => $success,
        'bg-yellow-100 text-yellow-800' => $warning,
        'bg-orange-100 text-orange-800' => $warningSecondary
    ])
>
    {{ $slot }}
</span>

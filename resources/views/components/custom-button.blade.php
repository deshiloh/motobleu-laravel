@props([
    'size' => 'md',
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'full' => false,
    'icon' => null,
    'iconRight' => null,
    'class' => '',
])

@php
    // Classes de base communes
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    // Gestion des tailles
    $sizeClasses = match($size) {
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-base',
        'xl' => 'px-6 py-3 text-lg',
        default => 'px-4 py-2 text-sm'
    };

    // Gestion des variantes de couleur (vous pouvez personnaliser ces classes)
    $variantClasses = match($variant) {
        'primary' => 'bg-motobleu hover:bg-blue-700 text-white focus:ring-blue-500',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white focus:ring-yellow-500',
        'info' => 'bg-cyan-600 hover:bg-cyan-700 text-white focus:ring-cyan-500',
        'outline-primary' => 'border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-500',
        'outline-secondary' => 'border border-gray-600 text-gray-600 hover:bg-gray-50 focus:ring-gray-500',
        'ghost' => 'text-gray-600 hover:bg-gray-100 focus:ring-gray-500',
        'link' => 'text-blue-600 hover:text-blue-700 hover:underline focus:ring-blue-500 p-0',
        default => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500'
    };

    // Classe pour la largeur complète
    $widthClass = $full ? 'w-full' : '';

    // Classes finales
    $finalClasses = trim($baseClasses . ' ' . $sizeClasses . ' ' . $variantClasses . ' ' . $widthClass . ' ' . $class);
@endphp

@if($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $finalClasses]) }}
        @if($disabled) aria-disabled="true" @endif
    >
        @if($icon)
            <x-icon :name="$icon" class="w-4 h-4 mr-2" />
        @endif

        {{ $slot }}

        @if($iconRight)
            <x-icon :name="$iconRight" class="w-4 h-4 ml-2" />
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $finalClasses]) }}
        @if($disabled) disabled @endif
    >
        @if($icon)
            <x-icon :name="$icon" class="w-4 h-4 mr-2" />
        @endif

        {{ $slot }}

        @if($iconRight)
            <x-icon :name="$iconRight" class="w-4 h-4 ml-2" />
        @endif
    </button>
@endif

@props(['isAcquitte', 'showToggle' => false, 'toggleAction' => null])

<div class="flex items-center space-x-2">
    @if($showToggle && $toggleAction)
        <button
            wire:click="{{ $toggleAction }}"
            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium transition-colors duration-200 {{ $isAcquitte ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}"
        >
            {{ $isAcquitte ? 'Acquittée' : 'Non acquittée' }}
        </button>
    @else
        @if($isAcquitte)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                Oui
            </span>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                Non
            </span>
        @endif
    @endif
</div>
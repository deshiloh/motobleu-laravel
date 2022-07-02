@props([
    'sortable' => null,
    'direction' => null
])
<th {{ $attributes->merge(['class' => 'px-6 py-3']) }}>
    @unless($sortable)
        <div>{{ $slot }}</div>
        @else
        <button class="flex space-x-2 group">
            <div class="uppercase text-xs font-bold">{{ $slot }}</div>
            <span class="text-gray-600 opacity-0 group-hover:opacity-100 transition duration-300">
                @if($direction === 'asc')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                    </svg>
                @endif
                @if($direction === 'desc')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                @endif
            </span>
        </button>
    @endunless
</th>

@props([
    'sortable' => null,
    'direction' => null,
    'active' => false
])

<th scope="col" {{ $attributes->merge(['class' => 'py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-2']) }}>
    @unless($sortable)
        {{ $slot }}
        @else
        <a href="#" class="group inline-flex">
            {{ $slot }}
            <!-- Active: "bg-gray-200 text-gray-900 group-hover:bg-gray-300", Not Active: "invisible text-gray-400 group-hover:visible group-focus:visible" -->
            <span
                @class([
                    'ml-2 flex-none rounded',
                    'bg-gray-200 text-gray-900 group-hover:bg-gray-300' => $active,
                    'invisible text-gray-400 group-hover:visible group-focus:visible' => !$active
                ])
            >
              <!-- Heroicon name: solid/chevron-down -->
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                       aria-hidden="true">
                      <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"/>
                  </svg>
            </span>
        </a>
    @endunless
</th>


{{--<th {{ $attributes }}>
    @unless($sortable)
        {{ $slot }}
        @else
        <button class="flex space-x-2 group uppercase">
            <span>{{ $slot }}</span>
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
</th>--}}

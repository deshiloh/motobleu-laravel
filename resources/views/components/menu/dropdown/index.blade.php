@props([
    'title' => '',
    'active' => false
])
<div class="relative" x-data="{isOpen : false}">
    <div>
        <button
            type="button"
            @class([
                'px-3 py-2 rounded-md text-sm font-medium',
                'text-gray-300 hover:bg-gray-700 hover:text-white ' => !$active,
                'bg-gray-900 text-white' => $active
            ])
            aria-expanded="false"
            aria-haspopup="true"
            @click="isOpen = true"
        >
            <span class="sr-only">Open dropdown</span>
            <div class="flex items-center space-x-2">
                <span>
                    {{ $title }}
                </span>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </span>
            </div>
        </button>
    </div>

    <!--
      Dropdown menu, show/hide based on menu state.

      Entering: "transition ease-out duration-100"
        From: "transform opacity-0 scale-95"
        To: "transform opacity-100 scale-100"
      Leaving: "transition ease-in duration-75"
        From: "transform opacity-100 scale-100"
        To: "transform opacity-0 scale-95"
    -->
    <div
        x-show="isOpen"
        @click.outside="isOpen = false"
        x-transition:enter="transition ease-out duration-100 transform"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75 transform"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1"
    >
        {{ $slot }}
    </div>
</div>

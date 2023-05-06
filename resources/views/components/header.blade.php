@props([
    'right' => false
])
<header class="bg-white shadow-sm dark:bg-slate-700 mb-4">
    <div class="py-4 px-4 sm:px-6 lg:px-4 flex justify-between items-center flex-col lg:flex-row">
        <h1 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white">
            {{ $slot }}
        </h1>
        @if($right)
            {{ $right }}
        @endif
    </div>
</header>

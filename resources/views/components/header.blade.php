@props([
    'right' => false
])
<header class="bg-white shadow-sm dark:bg-slate-700">
    <div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-lg leading-6 font-semibold text-gray-900 dark:text-white">
            {{ $slot }}
        </h1>
        @if($right)
            {{ $right }}
        @endif
    </div>
</header>

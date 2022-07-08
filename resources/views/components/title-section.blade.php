@props([
    'content' => ''
])
<h2 class="text-2xl bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm">
    <div class="flex items-center">
        <div class="content">
            {{ $title }}
        </div>
        <div class="slot ml-auto">
            {{ $slot }}
        </div>
    </div>
</h2>

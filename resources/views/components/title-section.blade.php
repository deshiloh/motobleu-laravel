@props([
    'content' => ''
])
<h2 class="text-2xl bg-base-100 p-4 rounded-lg shadow-sm">
    <div class="flex items-center">
        <div class="content">
            {{ $title }}
        </div>
        <div class="slot ml-auto flex items-center space-x-4">
            {{ $slot }}
        </div>
    </div>
</h2>

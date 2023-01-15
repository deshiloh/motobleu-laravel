@props(['button' => false])
<div class="flex items-center justify-between border-gray-200 border-b pb-3 mb-3">
    <h2 class="font-bold text-2xl">
        {{ $slot }}
    </h2>
    {{ $button }}
</div>


@props([
    'error' => null
])
<p id="helper-text-explanation" @class(['mt-2 text-sm', 'text-gray-500 dark:text-gray-400' => !$error, 'text-red-600 dark:text-red-500' => $error])>
    {{ $slot }}
</p>

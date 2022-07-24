@props([
    'title' => '',
    'description' => ''
])
<!-- This example requires Tailwind CSS v2.0+ -->
<div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            {{ $description }}
        </p>
    </div>
    <div class="border-t border-gray-200 dark:border-gray-500 px-4 py-5 sm:p-0">
        <dl class="sm:divide-y sm:divide-gray-200 dark:divide-gray-600">
            {{ $slot }}
        </dl>
    </div>
</div>

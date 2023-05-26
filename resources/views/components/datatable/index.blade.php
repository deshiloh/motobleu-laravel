@props([
    'headers' => false,
    'footer' => false
])
<div class="flex flex-col" wire:loading.class="opacity-25">
    <div class="-my-2 -mx-4 sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
            <div class="shadow ring-1 ring-black dark:ring-slate-900 ring-opacity-5 md:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-slate-900">
                    <thead class="bg-gray-50 dark:bg-slate-700">
                        {{ $headers }}
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-slate-800">
                        {{ $body }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

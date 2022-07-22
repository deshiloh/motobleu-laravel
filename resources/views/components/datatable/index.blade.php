@props([
    'headers' => false,
    'footer' => false
])
<div class="flex flex-col">
    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-500">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        {{ $headers }}
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-slate-800">
                        {{ $body }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

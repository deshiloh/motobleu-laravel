@props([
    'heads' => '',
])

@php
    $heads = explode(', ', $heads);
@endphp

<table class="table text-gray-400 border-separate space-y-6 text-sm w-full bg-white dark:bg-gray-900 mt-3 p-2 rounded-lg shadow-sm">
    <thead class="bg-white dark:bg-gray-900">
        <tr>
            @foreach($heads as $head)
                <th class="text-left p-3 first:rounded-l-lg">{{ $head }}</th>
            @endforeach
            <th class="text-left rounded-r-lg p-3">Actions</th>
        </tr>
    </thead>
    <tbody>
        {{ $slot }}
    </tbody>
</table>

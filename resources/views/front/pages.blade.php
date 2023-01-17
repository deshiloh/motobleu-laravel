<x-front-layout>
    <div class="max-w-6xl mx-auto w-full">
        <x-front.card>
            <h1 class="font-bold text-2xl">
                {{ $page->title }}
            </h1>
        </x-front.card>
        <x-front.card>
            {!! $page->content !!}
        </x-front.card>
    </div>
</x-front-layout>

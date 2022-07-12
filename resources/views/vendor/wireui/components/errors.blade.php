@if ($hasErrors($errors))
    <div {{ $attributes->merge(['class' => 'rounded-lg alert alert-error shadow-lg']) }}>
        <div>
            <div class="flex flex-col">
                <div class="flex items-center">
                    <x-dynamic-component
                        :component="WireUi::component('icon')"
                        class="w-5 h-5 shrink-0 mr-3"
                        name="exclamation-circle"
                    />
                    {{ str_replace('{errors}', $count($errors), $title) }}
                </div>
                <div>
                    <ul class="list-disc space-y-1 text-sm pl-6 mt-4">
                        @foreach ($getErrorMessages($errors) as $message)
                            <li>{{ head($message) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="hidden"></div>
@endif


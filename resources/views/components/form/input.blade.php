@props([
    'label' => false,
    'name' => '',
    'required' => false,
    'value' => false,
    'helpText' => false
])
<div class="form-group">
    @if($label)
        <label
            @class([
            "after:content-['*'] after:ml-0.5 after:text-red-500" => $required,
            'mb-1 block'
            ])
        >
            {{ $label }}
        </label>
    @endif
    <input
        {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
        ]) }}
        @if($required) required="required" @endif
        name="{{ $name }}"
        value="{{ (@old($name) !== null) ? @old($name) : $value}}"
    >
    @if($helpText)
        <p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ $helpText }}
        </p>
    @endif
    @error($name)
        <x-form.helper error>{{ $message }}</x-form.helper>
    @enderror
</div>

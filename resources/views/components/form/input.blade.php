@props([
    'type' => 'text',
    'label' => '',
    'name' => '',
    'required' => false,
    'value' => false,
    'helpText' => false
])
<div class="form-group">
    <label
        @class([
        "after:content-['*'] after:ml-0.5 after:text-red-500" => $required,
        'mb-1 block'
        ])
    >
        {{ $label }}
    </label>
    <input
        type="{{ $type }}"
        class="form-control
        block
        w-full
        px-3
        py-1.5
        text-base
        font-normal
        text-gray-700 dark:text-gray-100
        bg-white bg-clip-padding dark:bg-gray-800
        border border-solid border-gray-300 dark:border-gray-800
        rounded
        transition
        ease-in-out
        m-0
        focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
        name="{{ $name }}"
        @if($required) required="required" @endif
        value="{{ (!$value) ? @old($name) : $value}}"
    >
    @if($helpText)
        <div class="text-sm text-gray-500 mt-1">{{ $helpText }}</div>
    @endif
    @error($name)
        <div class="text-red-600">{{ $message }}</div>
    @enderror
</div>

@props([
    'type' => 'text',
    'placeholder' => '',
    'name' => ''
])
<label>
    <input
        type="{{ $type }}"
        class="border border-gray-400 rounded-md py-1 px-1 w-full my-2"
        placeholder="{{ $placeholder }}"
        name="{{ $name }}"
    >
</label>

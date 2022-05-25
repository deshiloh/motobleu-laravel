@props([
    'datas' => [],
    'label' => '',
    'name' => '',
    'required' => false,
    'selected' => ''
])
<div class="form-group">
    <label for="">{{ $label }}</label>

    <select name="{{ $name }}" {{ $attributes->merge(['class' => 'mt-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" aria-label="Default select example']) }}>
        <option value="">Choisir une valeur</option>
        @foreach($datas as $key => $value)
            <option @if($key == $selected) selected @endif value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>

    @error($name) <x-form.helper error>{{ $message }}</x-form.helper> @enderror
</div>

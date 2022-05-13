@props([
    'datas' => [],
    'label' => 'Label',
    'name' => '',
    'required' => false,
    'selected' => false
])
<div class="form-group">
    <label for="">{{ $label }}</label>
    <select name="{{ $name }}" class="form-select appearance-none
      block
      w-full
      px-3
      py-1.5
      text-base
      font-normal
      text-gray-700 dark:text-gray-100
      bg-white bg-clip-padding bg-no-repeat dark:bg-gray-800
      border border-solid border-gray-300 dark:border-gray-900
      rounded
      transition
      ease-in-out
      m-0
      focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" aria-label="Default select example">
        <option>Choisir une valeur</option>
        @foreach($datas as $key => $value)
            <option @if($value == $selected) selected @endif value="{{ $value }}">{{ $key }}</option>
        @endforeach
    </select>
</div>

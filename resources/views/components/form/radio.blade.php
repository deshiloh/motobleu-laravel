<div class="form-control">
    <label class="label cursor-pointer space-x-2">
        <input type="radio" {{ $attributes->merge(['class' => 'radio']) }} />
        <span class="label-text">
           {{ $slot }}
        </span>
    </label>
</div>

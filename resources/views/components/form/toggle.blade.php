<div class="form-control">
    <label class="label cursor-pointer flex items-center justify-start space-x-2">
        <input type="checkbox" {{ $attributes->merge(['class' => 'toggle toggle-sm']) }}/>
        <span class="label-text">
            {{ $slot }}
        </span>
    </label>
</div>
